<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\BankDetailsService;
use Illuminate\Http\Request;

class BankDetailsController extends Controller
{
    protected $bankDetailsService;

    public function __construct(BankDetailsService $bankDetailsService)
    {
        $this->bankDetailsService = $bankDetailsService;
    }

    // Fetch admin bank details
    public function index(Request $request)
    {
        try {
            // Assuming we're fetching bank details for a specific admin (e.g., user_id = 1)
            // You can adjust this logic to fetch the appropriate admin's bank details

            $bankDetails = $this->bankDetailsService->getBankDetails();

            if ($bankDetails->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No bank details found.',
                    'data' => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bank details retrieved successfully.',
                'data' => $bankDetails->map(function ($bankDetail) {
                    return [
                        'id' => $bankDetail->id,
                        'bank_name' => $bankDetail->bank_name,
                        'account_name' => $bankDetail->account_name,
                        'account_number' => $bankDetail->account_number,
                        'ifsc_code' => $bankDetail->ifsc_code,
                        'swift_code' => $bankDetail->swift_code,
                        'created_at' => $bankDetail->created_at,
                        'updated_at' => $bankDetail->updated_at
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bank details: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}