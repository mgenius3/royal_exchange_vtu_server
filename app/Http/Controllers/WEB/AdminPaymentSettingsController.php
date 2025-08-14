<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Services\PaymentConfigService;
use Illuminate\Http\Request;

class AdminPaymentSettingsController extends Controller
{
    protected $service;

    public function __construct(PaymentConfigService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $configs = $this->service->all()->keyBy('gateway');
        return view('bank_details.payment-settings', compact('configs'));
    }

    public function update(Request $request, $gateway)
    {
        $data = $request->only([
            'public_key', 'secret_key', 'encryption_key', 'webhook_secret', 'is_active'
        ]);
        $data['is_active'] = $request->has('is_active');
        $this->service->update($gateway, $data);

        return redirect()->back()->with('success', ucfirst($gateway).' settings updated!');
    }
}
