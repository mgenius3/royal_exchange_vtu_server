<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AirtimeRequest;
use App\Models\BettingRequest;
use App\Models\CableTvRequest;
use App\Models\DataRequest;
use App\Models\ElectricityRequest;
use App\Models\VtuOrder;
use App\Services\EbillsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\WalletTransaction;
use App\Services\TransactionLogger;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class EbillsController extends Controller
{
    protected $ebills;
    protected $userService;


    public function __construct(EbillsService $ebills,  UserService $userService)
    {
        $this->ebills = $ebills;
        $this->userService = $userService;
    }

    /**
     * Fetch available data variations from eBills Africa API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataVariations(Request $request)
    {
        try {
            $response = $this->ebills->makeApiRequest('api/v2/variations/data', [], 'GET');
            return response()->json([
                'status' => 'success',
                'data' => $response,
                'message' => 'Data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch data variations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Purchase data via eBills Africa API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => [
                    'required',
                    'string',
                    'regex:/^(\+234[0-9]{10}|[0-9]{11})$/', // Supports +234 or 11-digit format
                ],
                'service_id' => [
                    'required',
                    'string',
                    'in:MTN,Glo,Airtel,9mobile,Smile',
                    function ($attribute, $value, $fail) use ($request) {
                        $network = $this->getNetworkFromPhone($request->input('phone'));
                        if ($value !== $network) {
                            $fail('The service_id does not match the phone number’s network.');
                        }
                    },
                ],
                'variation_id' => 'required|string',
                'request_id' => 'required|string', // Ensure unique request_id
                'user_id' => 'required|string',
                'amount' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            $reference = 'data_withdrawal_' . uniqid();

            $response = $this->ebills->makeApiRequest('api/v2/data', $data);

            // Check API response
            if (!isset($response['code']) || $response['code'] !== 'success') {
                // API call failed or returned an error
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data purchase failed: ' . ($response['message'] ?? 'Unknown error'),
                ], 400);
            }

            $orderStatus = $response['data']['status'] ?? null;
            $amount = $data['amount'];

            // Prepare receipt data
            $receiptData = [
                'disco' => $data['service_id'],
                'phone' => $data['phone'],
                'variation_id' => $data['variation_id'],
                'amount' => $amount,
                'response' => $response,
                'timestamp' => now()->toIso8601String()
            ];

            // Store order details
            $final_response =  VtuOrder::create([
                'order_id' => $response['data']['order_id'],
                'request_id' => $data['request_id'],
                'user_id' => $data['user_id'],
                'product_name' => $response['data']['product_name'],
                'status' => $response['data']['status'],
                'amount' => $response['data']['amount'],
                'amount_charged' => $response['data']['amount_charged'],
                'meta_data' => $response['data']['meta_data'] ?? null,
                'receipt_data' => $receiptData, // Store receipt data
                'date_created' => now(),
                'date_updated' => now()
            ]);

            if ($orderStatus === 'processing-api' || $orderStatus === 'completed-api') {
                // Deduct from wallet
                $user = $this->userService->deductWallet($data['user_id'], $amount);
                if (!$user) {
                    // Refund not needed since no deduction occurred
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient wallet balance for this transaction'
                    ], 400);
                }

                // Record successful transaction
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => -$amount,
                    'type' => 'withdrawal',
                    'status' => 'success',
                    'gateway' => 'Data'
                ]);


                // Log the transaction attempt
                TransactionLogger::log(
                    transactionType: 'data_purchase',
                    referenceId: $data['request_id'],
                    details: [
                        'total_amount' => $data['amount'],
                        'message' => "Transaction for data plan {$data['service_id']}",
                        'type' => 'Buy'
                    ],
                    success: true // Initially false
                );

                // Store request_id
                DataRequest::create(['request_id' => $data['request_id']]);

                return response()->json([
                    'status' => 'success',
                    'data' => $final_response,
                    'message' => 'Data purchase ' . ($orderStatus === 'processing-api' ? 'initiated' : 'completed') . ' successfully',
                ]);
            } elseif ($orderStatus === 'refunded') {
                // Order was refunded by API
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => 0,
                    'type' => 'withdrawal',
                    'status' => 'failed',
                    'gateway' => 'Data'
                ]);


                return response()->json([
                    'status' => 'error',
                    'data' => $response,
                    'message' => 'Data purchase was refunded by the provider.'
                ], 400);
            } else {
                // Unknown or failed status
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => 0,
                    'type' => 'withdrawal',
                    'status' => 'failed',
                    'gateway' => 'Data'
                ]);

                return response()->json([
                    'status' => 'error',
                    'data' => $response,
                    'message' => 'Data purchase failed due to an unknown status: ' . $orderStatus
                ], 400);
            }
        } catch (\Exception $e) {
            $errorMessage = $this->extractErrorMessage($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process airtime purchase: ' . $errorMessage
            ], 500);
        }
    }

    /**
     * Purchase airtime via eBills Africa API (from previous conversation).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyAirtime(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => [
                    'required',
                    'string',
                    'regex:/^(\+234[0-9]{10}|[0-9]{11})$/',
                ],
                'amount' => [
                    'required',
                    'numeric',
                    function ($attribute, $value, $fail) use ($request) {
                        $network = $this->getNetworkFromPhone($request->input('phone'));
                        $minAmount = $network === 'MTN' ? 10 : 50;
                        if ($value < $minAmount || $value > 50000) {
                            $fail("The amount must be between ₦{$minAmount} and ₦50,000.");
                        }
                    },
                ],
                'service_id' => [
                    'required',
                    'string',
                    'in:MTN,Glo,Airtel,9mobile',
                    function ($attribute, $value, $fail) use ($request) {
                        $network = $this->getNetworkFromPhone($request->input('phone'));
                        if ($value !== $network) {
                            $fail('The service_id does not match the phone number’s network.');
                        }
                    },
                ],
                'request_id' => 'required|string',
                'user_id' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // // Deduct from wallet
            // $user = $this->userService->deductWallet($request->user_id, $request->amount);
            // if (!$user) {
            //     throw new \Exception('Insufficient wallet balance for this transaction');
            // }

            $reference = 'airtime_withdrawal_' . uniqid();


            $response = $this->ebills->makeApiRequest('api/v2/airtime', $data);

            // Check API response
            if (!isset($response['code']) || $response['code'] !== 'success') {
                // API call failed or returned an error
                return response()->json([
                    'status' => 'error',
                    'message' => 'Betting purchase failed: ' . ($response['message'] ?? 'Unknown error'),
                ], 400);
            }

            // Handle different order statuses
            $orderStatus = $response['data']['status'] ?? null;
            $amount = $data['amount'];

            // Prepare receipt data
            $receiptData = [
                'disco' => $data['service_id'],
                'phone' => $data['phone'],
                'amount' => $amount,
                'response' => $response,
                'timestamp' => now()->toIso8601String(),
            ];


            // Store order details
            $final_response =  VtuOrder::create([
                'order_id' => $response['data']['order_id'],
                'request_id' => $data['request_id'],
                'user_id' => $data['user_id'],
                'product_name' => $response['data']['product_name'],
                'status' => $response['data']['status'],
                'amount' => $response['data']['amount'],
                'amount_charged' => $response['data']['amount_charged'],
                'meta_data' => $response['data']['meta_data'] ?? null,
                'receipt_data' => $receiptData, // Store receipt data
                'date_created' => now(),
                'date_updated' => now()
            ]);

            if ($orderStatus === 'processing-api' || $orderStatus === 'completed-api') {
                // Deduct from wallet
                $user = $this->userService->deductWallet($data['user_id'], $amount);
                if (!$user) {
                    // Refund not needed since no deduction occurred
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient wallet balance for this transaction'
                    ], 400);
                }

                // Record successful transaction
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => -$amount,
                    'type' => 'withdrawal',
                    'status' => 'success',
                    'gateway' => 'Airtime'
                ]);

                  // Log the transaction attempt
                  TransactionLogger::log(
                    transactionType: 'airtime_purchase',
                    referenceId: $data['request_id'],
                    details: [
                        'total_amount' => $data['amount'],
                        'message' => "Transaction for Airtime {$data['service_id']}",
                        'type' => 'Buy'
                    ],
                    success: true // Initially false
                );

                // Store request_id
                AirtimeRequest::create(['request_id' => $data['request_id']]);

                return response()->json([
                    'status' => 'success',
                    'data' => $final_response,
                    'message' => 'Airtime purchase ' . ($orderStatus === 'processing-api' ? 'initiated' : 'completed') . ' successfully',
                ]);
            } elseif ($orderStatus === 'refunded') {
                // Order was refunded by API
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => 0,
                    'type' => 'withdrawal',
                    'status' => 'failed',
                    'gateway' => 'Airtime'
                ]);


                return response()->json([
                    'status' => 'error',
                    'data' => $final_response,
                    'message' => 'Airtime purchase was refunded by the provider.'
                ], 400);
            } else {
                // Unknown or failed status
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => 0,
                    'type' => 'withdrawal',
                    'status' => 'failed',
                    'gateway' => 'Airtime'
                ]);

                return response()->json([
                    'status' => 'error',
                    'data' => $final_response,
                    'message' => 'Airtime purchase failed due to an unknown status: ' . $orderStatus
                ], 400);
            }
        } catch (\Exception $e) {
            $errorMessage = $this->extractErrorMessage($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process airtime purchase: ' . $errorMessage
            ], 500);
        }
    }
    /**
     * Determine network based on phone number prefix.
     *
     * @param string $phone
     * @return string|null
     */
    protected function getNetworkFromPhone($phone)
    {
        $phone = preg_replace('/^\+234/', '0', $phone);

        $prefixes = [
            'MTN' => ['0803', '0806', '0703', '0706', '0810', '0813', '0814', '0816', '0903', '0906', '0913', '0916'],
            'Glo' => ['0805', '0807', '0811', '0815', '0705', '0905'],
            'Airtel' => ['0802', '0808', '0812', '0701', '0708', '0902', '0907'],
            '9mobile' => ['0809', '0817', '0818', '0908', '0909'],
            'Smile' => ['0702'], // Add Smile prefixes if known
        ];

        foreach ($prefixes as $network => $prefixList) {
            foreach ($prefixList as $prefix) {
                if (str_starts_with($phone, $prefix)) {
                    return $network;
                }
            }
        }

        return null;
    }
    /**
     * Purchase electricity via eBills Africa API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function buyElectricity(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{11,13}$/',
                ],
                'service_id' => [
                    'required',
                    'string',
                    'in:ikeja-electric,eko-electric,kaduna-electric,jos-electric,ibadan-electric,portharcourt-electric,abuja-electric,kano-electric,benin-electric,yola-electric,aba-electric,enugu-electric',
                ],
                'variation_id' => [
                    'required',
                    'string',
                    'in:prepaid,postpaid',
                ],
                'amount' => [
                    'required',
                    'numeric',
                    'min:1223.57', // Updated based on previous error
                    'max:100000',
                ],
                'request_id' => 'required|string',
                'user_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $reference = 'electricity_withdrawal_' . uniqid();

            // Make API request
            $response = $this->ebills->makeApiRequest('api/v2/electricity', $data);



            // Check API response
            if (!isset($response['code']) || $response['code'] !== 'success') {
                // API call failed or returned an error
                return response()->json([
                    'status' => 'error',
                    'message' => 'Electricity purchase failed: ' . ($response['message'] ?? 'Unknown error'),
                ], 400);
            }

            // Handle different order statuses
            $orderStatus = $response['data']['status'] ?? null;
            $amount = $data['amount'];

            // Prepare receipt data
            $receiptData = [
                'disco' => $data['service_id'],
                'customer_id' => $data['customer_id'],
                'variation_id' => $data['variation_id'],
                'amount' => $amount,
                'response' => $response,
                'timestamp' => now()->toIso8601String(),
            ];

            // Store order details
            $final_response = VtuOrder::create([
                'order_id' => $response['data']['order_id'],
                'request_id' => $data['request_id'],
                'user_id' => $data['user_id'],
                'product_name' => $response['data']['product_name'],
                'status' => $response['data']['status'],
                'amount' => $response['data']['amount'],
                'amount_charged' => $response['data']['amount_charged'],
                'meta_data' => $response['data']['meta_data'] ?? null,
                'receipt_data' => $receiptData, // Store receipt data
                'date_created' => now(),
                'date_updated' => now(),
            ]);

            if ($orderStatus === 'processing-api' || $orderStatus === 'completed-api') {
                // Deduct from wallet
                $user = $this->userService->deductWallet($data['user_id'], $amount);
                if (!$user) {
                    // Refund not needed since no deduction occurred
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient wallet balance for this transaction',
                    ], 400);
                }

                // Record successful transaction
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => -$amount,
                    'type' => 'withdrawal',
                    'status' => 'success',
                    'gateway' => 'Electricity'
                ]);

                 // Log the transaction attempt
                 TransactionLogger::log(
                    transactionType: 'electricity_purchase',
                    referenceId: $data['request_id'],
                    details: [
                        'total_amount' => $data['amount'],
                        'message' => "Transaction for electricity ({$data['service_id']})",
                        'type' => 'Buy'
                    ],
                    success: true // Initially false
                );

                // Store request_id
                ElectricityRequest::create(['request_id' => $data['request_id']]);

                return response()->json([
                    'status' => 'success',
                    'data' => $final_response,
                    'message' => 'Electricity purchase ' . ($orderStatus === 'processing-api' ? 'initiated' : 'completed') . ' successfully',
                ]);
            } elseif ($orderStatus === 'refunded') {
                // Unknown or failed status
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => 0,
                    'type' => 'withdrawal',
                    'status' => 'failed',
                    'gateway' => 'Electricity'
                ]);

                return response()->json([
                    'status' => 'error',
                    'data' => $response,
                    'message' => 'Electricity purchase was refunded by the provider.'
                ], 400);
            } else {
                // Unknown or failed status
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => 0,
                    'type' => 'withdrawal',
                    'status' => 'failed',
                    'gateway' => 'Electricity'
                ]);

                return response()->json([
                    'status' => 'error',
                    'data' => $response,
                    'message' => 'Electricity purchase failed due to an unknown status: ' . $orderStatus,
                ], 400);
            }
        } catch (\Exception $e) {
            // Handle API or server errors
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'below_minimum_amount')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The amount entered is below the minimum of ₦1223.57. Please enter a higher amount.',
                ], 400);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process electricity purchase: ' . $errorMessage
            ], 500);
        }
    }


    public function buyBetting(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => [
                    'required',
                    'string'
                ],
                'service_id' => [
                    'required',
                    'string',
                    'in:1xBet,BangBet,Bet9ja,BetKing,BetLand,BetLion,BetWay,CloudBet,LiveScoreBet,MerryBet,NaijaBet,NairaBet,SupaBet',
                ],
                'amount' => [
                    'required',
                    'numeric',
                    'min:100', // Updated based on previous error
                    'max:100000',
                ],
                'request_id' => 'required|string',
                'user_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $reference = 'betting_withdrawal_' . uniqid();

            // Make API request
            $response = $this->ebills->makeApiRequest('api/v2/betting', $data);

            // Check API response
            if (!isset($response['code']) || $response['code'] !== 'success') {
                // API call failed or returned an error
                return response()->json([
                    'status' => 'error',
                    'message' => 'Betting purchase failed: ' . ($response['message'] ?? 'Unknown error'),
                ], 400);
            }

            // Handle different order statuses
            $orderStatus = $response['data']['status'] ?? null;
            $amount = $data['amount'];


            // Prepare receipt data
            $receiptData = [
                'disco' => $data['service_id'],
                'customer_id' => $data['customer_id'],
                'amount' => $amount,
                'response' => $response,
                'timestamp' => now()->toIso8601String(),
            ];

            // Store order details
            $final_response =  VtuOrder::create([
                'order_id' => $response['data']['order_id'],
                'request_id' => $data['request_id'],
                'user_id' => $data['user_id'],
                'product_name' => $response['data']['product_name'],
                'status' => $response['data']['status'],
                'amount' => $response['data']['amount'],
                'amount_charged' => $response['data']['amount_charged'],
                'meta_data' => $response['data']['meta_data'] ?? null,
                'receipt_data' => $receiptData, // Store receipt data
                'date_created' => now(),
                'date_updated' => now()
            ]);

            if ($orderStatus === 'processing-api' || $orderStatus === 'completed-api') {
                // Deduct from wallet
                $user = $this->userService->deductWallet($data['user_id'], $amount);
                if (!$user) {
                    // Refund not needed since no deduction occurred
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient wallet balance for this transaction'
                    ], 400);
                }

                // Record successful transaction
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => -$amount,
                    'type' => 'withdrawal',
                    'status' => 'success',
                    'gateway' => 'Betting'
                ]);

                 // Log the transaction attempt
                 TransactionLogger::log(
                    transactionType: 'betting_purchase',
                    referenceId: $data['request_id'],
                    details: [
                        'total_amount' => $data['amount'],
                        'message' => "Transaction for betting ({$data['service_id']})",
                        'type' => 'Buy'
                    ],
                    success: true // Initially false
                );

                // Store request_id
                BettingRequest::create(['request_id' => $data['request_id']]);

                return response()->json([
                    'status' => 'success',
                    'data' => $final_response,
                    'message' => 'Betting purchase ' . ($orderStatus === 'processing-api' ? 'initiated' : 'completed') . ' successfully',
                ]);
            } elseif ($orderStatus === 'refunded') {
                // Order was refunded by API
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => 0,
                    'type' => 'withdrawal',
                    'status' => 'failed',
                    'gateway' => 'Betting'
                ]);


                return response()->json([
                    'status' => 'error',
                    'data' => $response,
                    'message' => 'Betting purchase was refunded by the provider.'
                ], 400);
            } else {
                // Unknown or failed status
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => 0,
                    'type' => 'withdrawal',
                    'status' => 'failed',
                    'gateway' => 'Betting'
                ]);

                return response()->json([
                    'status' => 'error',
                    'data' => $response,
                    'message' => 'Betting purchase failed due to an unknown status: ' . $orderStatus
                ], 400);
            }
        } catch (\Exception $e) {
            // Handle API or server errors
            $errorMessage = $e->getMessage();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process electricity purchase: ' . $errorMessage,
            ], 500);
        }
    }


    public function getTvVariations(Request $request)
    {
        try {
            $response = $this->ebills->makeApiRequest('api/v2/variations/tv', [], 'GET');
            return response()->json([
                'status' => 'success',
                'data' => $response,
                'message' => 'Tv retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch tv variations: ' . $e->getMessage()
            ], 500);
        }
    }


    public function buyTv(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => [
                    'required',
                    'string'
                ],
                'service_id' => [
                    'required',
                    'string',
                    'in:dstv,gotv,startimes,showmax'
                ],
                'variation_id' => 'required|string',
                'request_id' => 'required|string', // Ensure unique request_id
                'user_id' => 'required|string',
                'amount' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $data = $validator->validated();
            $reference = 'tv_withdrawal_' . uniqid();

            $response = $this->ebills->makeApiRequest('api/v2/tv', $data);

            // Check API response
            if (!isset($response['code']) || $response['code'] !== 'success') {
                // API call failed or returned an error
                return response()->json([
                    'status' => 'error',
                    'message' => 'Betting purchase failed: ' . ($response['message'] ?? 'Unknown error'),
                ], 400);
            }

            // Handle different order statuses
            $orderStatus = $response['data']['status'] ?? null;
            $amount = $data['amount'];

            // Prepare receipt data
            $receiptData = [
                'disco' => $data['service_id'],
                'customer_id' => $data['customer_id'],
                'variation_id' => $data['variation_id'],
                'amount' => $amount,
                'response' => $response,
                'timestamp' => now()->toIso8601String()
            ];

            // Store order details
            $final_response = VtuOrder::create([
                'order_id' => $response['data']['order_id'],
                'request_id' => $data['request_id'],
                'user_id' => $data['user_id'],
                'product_name' => $response['data']['product_name'],
                'status' => $response['data']['status'],
                'amount' => $response['data']['amount'],
                'amount_charged' => $response['data']['amount_charged'],
                'meta_data' => $response['data']['meta_data'] ?? null,
                'receipt_data' => $receiptData, // Store receipt data
                'date_created' => now(),
                'date_updated' => now()
            ]);

            if ($orderStatus === 'processing-api' || $orderStatus === 'completed-api') {
                // Deduct from wallet
                $user = $this->userService->deductWallet($data['user_id'], $amount);
                if (!$user) {
                    // Refund not needed since no deduction occurred
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient wallet balance for this transaction'
                    ], 400);
                }

                // Record withdrawal transaction
                WalletTransaction::create([
                    'user_id' => $request->user_id,
                    'reference' => $reference,
                    'amount' => -$request->amount, // Negative for withdrawal
                    'type' => 'withdrawal',
                    'status' => 'success',
                    'gateway' => 'Tv'
                ]);

                 // Log the transaction attempt
                 TransactionLogger::log(
                    transactionType: 'tv_purchase',
                    referenceId: $data['request_id'],
                    details: [
                        'total_amount' => $data['amount'],
                        'message' => "Transaction for tv ({$data['service_id']})",
                        'type' => 'Buy'
                    ],
                    success: true // Initially false
                );


                // Store request_id
                CableTvRequest::create(['request_id' => $data['request_id']]);

                return response()->json(
                    [
                        'status' => 'success',
                        'data' => $final_response,
                        'message' => 'Tv purchased successfully' . ($orderStatus === 'processing-api' ? 'initiated' : 'completed') . ' successfully'
                    ]
                );
            } elseif ($orderStatus === 'refunded') {
                return response()->json([
                    'status' => 'error',
                    'data' => $response,
                    'message' => 'Tv purchase was refunded by the provider.'
                ], 400);
            } else {
                // Unknown or failed status
                WalletTransaction::create([
                    'user_id' => $data['user_id'],
                    'reference' => $reference,
                    'amount' => 0,
                    'type' => 'withdrawal',
                    'status' => 'failed',
                    'gateway' => 'Tv'
                ]);

                return response()->json([
                    'status' => 'error',
                    'data' => $response,
                    'message' => 'Tv purchase failed due to an unknown status: ' . $orderStatus
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process tv purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    // ... Existing methods (getDataVariations, buyData, buyAirtime, buyElectricity, buyBetting, getNetworkFromPhone) unchanged ...

    /**
     * Verify customer identity via eBills Africa API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCustomer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => [
                    'required',
                    'string',
                ],
                'service_id' => [
                    'required',
                    'string',
                    // List all supported service IDs
                    'in:ikeja-electric,eko-electric,kaduna-electric,jos-electric,ibadan-electric,portharcourt-electric,abuja-electric,kano-electric,benin-electric,yola-electric,aba-electric,enugu-electric,' .
                        '1xBet,BangBet,Bet9ja,BetKing,BetLand,BetLion,BetWay,CloudBet,LiveScoreBet,MerryBet,NaijaBet,NairaBet,SupaBet,' .
                        'dstv,gotv,startimes',
                ],
                'variation_id' => [
                    'nullable',
                    'string',
                    'in:prepaid,postpaid',
                    function ($attribute, $value, $fail) use ($request) {
                        // variation_id is required only for electricity services
                        $electricityServices = [
                            'ikeja-electric',
                            'eko-electric',
                            'kaduna-electric',
                            'jos-electric',
                            'ibadan-electric',
                            'portharcourt-electric',
                            'abuja-electric',
                            'kano-electric',
                            'benin-electric',
                            'yola-electric',
                            'aba-electric',
                            'enugu-electric',
                        ];
                        if (in_array($request->input('service_id'), $electricityServices) && !$value) {
                            $fail('The variation_id field is required for electricity services.');
                        }
                    },
                ],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // Prepare payload for eBills Africa API
            $payload = [
                'customer_id' => $data['customer_id'],
                'service_id' => $data['service_id'],
            ];

            if (isset($data['variation_id'])) {
                $payload['variation_id'] = $data['variation_id'];
            }

            // Call eBills Africa API to verify customer
            $response = $this->ebills->makeApiRequest('api/v2/verify-customer', $payload, 'POST');

            if (!isset($response['code']) || $response['code'] !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customer verification failed: ' . ($response['message'] ?? 'Unknown error'),
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'data' => $response['data'],
                'message' => 'Customer verified successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify customer: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch order details by request_id.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = VtuOrder::where('request_id', $request->input('request_id'))->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order,
            'message' => 'Order details retrieved successfully.'
        ]);
    }

    /**
     * Handle eBills Africa webhook notifications.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        try {
            // Get the raw request body
            $payload = $request->getContent();
            $signature = $request->header('X-Webhook-Signature');

            if (!$signature) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing webhook signature.',
                ], 400);
            }

            // Verify the HMAC-SHA256 signature
            $userPin = env('EBILLS_WEBHOOK_PIN'); // Store your eBills PIN in .env
            $expectedSignature = hash_hmac('sha256', $payload, $userPin);

            if (!hash_equals($expectedSignature, $signature)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid webhook signature.',
                ], 401);
            }

            // Decode the payload
            $data = json_decode($payload, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid JSON payload.',
                ], 400);
            }

            // Validate required fields
            $requiredFields = ['order_id', 'status', 'request_id', 'product_name', 'amount', 'amount_charged', 'date_created', 'date_updated'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Missing required field: $field.",
                    ], 400);
                }
            }

            // Find the order by order_id or request_id
            $order = VtuOrder::where('order_id', $data['order_id'])
                ->orWhere('request_id', $data['request_id'])
                ->first();

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found.',
                ], 404);
            }

            // Update the order status and other details
            $order->status = $data['status'];
            $order->amount = $data['amount'];
            $order->amount_charged = $data['amount_charged'];
            $order->meta_data = $data['meta_data'] ?? $order->meta_data;
            $order->date_updated = $data['date_updated'];
            $order->save();

            // If the order is refunded, credit the user's wallet
            if ($data['status'] === 'refunded') {
                $user = $this->userService->fundWallet($order->user_id, $order->amount);
                if (!$user) {
                    Log::error("Failed to credit wallet for user {$order->user_id} on refund for order {$order->order_id}");
                }

                // Update the corresponding WalletTransaction
                WalletTransaction::where('reference', 'like', "{$order->product_name}_withdrawal_%")
                    ->where('user_id', $order->user_id)
                    ->where('amount', -$order->amount)
                    ->update(['status' => 'refunded']);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook processed successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process webhook: ' . $e->getMessage(),
            ], 500);
        }
    }
}
