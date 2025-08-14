<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:sanctum'); // Requires API authentication
    }

    /**
     * Get all exchange rates
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $exchangeRates = ExchangeRate::all();
            return response()->json([
                'success' => true,
                'data' => $exchangeRates,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch exchange rates: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific exchange rate by currency code
     *
     * @param string $currencyCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($currencyCode)
    {
        try {
            $exchangeRate = ExchangeRate::where('currency_code', strtoupper($currencyCode))->firstOrFail();
            return response()->json([
                'success' => true,
                'data' => $exchangeRate
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exchange rate not found: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Create a new exchange rate
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string|size:3|unique:exchange_rates,currency_code',
            'rate' => 'required|numeric|min:0',
        ]);

        try {
            $exchangeRate = ExchangeRate::create([
                'currency_code' => strtoupper($request->currency_code),
                'rate' => $request->rate,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exchange rate created successfully',
                'data' => $exchangeRate,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create exchange rate: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing exchange rate
     *
     * @param \Illuminate\Http\Request $request
     * @param string $currencyCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $currencyCode)
    {
        $exchangeRate = ExchangeRate::where('currency_code', strtoupper($currencyCode))->firstOrFail();

        $request->validate([
            'currency_code' => 'required|string|size:3|unique:exchange_rates,currency_code,' . $exchangeRate->id,
            'rate' => 'required|numeric|min:0',
        ]);

        try {
            $exchangeRate->update([
                'currency_code' => strtoupper($request->currency_code),
                'rate' => $request->rate,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exchange rate updated successfully',
                'data' => $exchangeRate,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update exchange rate: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an exchange rate
     *
     * @param string $currencyCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($currencyCode)
    {
        try {
            $exchangeRate = ExchangeRate::where('currency_code', strtoupper($currencyCode))->firstOrFail();
            $exchangeRate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Exchange rate deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete exchange rate: ' . $e->getMessage(),
            ], 404);
        }
    }
}