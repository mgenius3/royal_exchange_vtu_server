<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class AdminExchangeRateController extends Controller
{
    public function index()
    {
        $exchangeRates = ExchangeRate::all();
        return view('exchange_rates_management.index', compact('exchangeRates'));
    }

    public function create()
    {
        return view('exchange_rates_management.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string|size:3|unique:exchange_rates,currency_code',
            'rate' => 'required|numeric|min:0',
        ]);

        ExchangeRate::create([
            'currency_code' => strtoupper($request->currency_code),
            'rate' => $request->rate,
        ]);

        return redirect()->route('admin.exchange-rates.index')->with('success', 'Exchange rate added successfully');
    }

    public function edit(ExchangeRate $exchangeRate)
    {
        return view('exchange_rates_management.edit', compact('exchangeRate'));
    }

    public function update(Request $request, ExchangeRate $exchangeRate)
    {
        $request->validate([
            'currency_code' => 'required|string|size:3|unique:exchange_rates,currency_code,' . $exchangeRate->id,
            'rate' => 'required|numeric|min:0',
        ]);

        $exchangeRate->update([
            'currency_code' => strtoupper($request->currency_code),
            'rate' => $request->rate,
        ]);

        return redirect()->route('admin.exchange-rates.index')->with('success', 'Exchange rate updated successfully');
    }

    public function destroy(ExchangeRate $exchangeRate)
    {
        $exchangeRate->delete();
        return redirect()->route('admin.exchange-rates.index')->with('success', 'Exchange rate deleted successfully');
    }
}