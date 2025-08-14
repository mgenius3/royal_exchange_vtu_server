<?php

namespace App\Services;

use App\Models\PaymentSetting;

class PaymentConfigService
{
    public function get($gateway)
    {
        return PaymentSetting::where('gateway', $gateway)->first();
    }

    public function all()
    {
        return PaymentSetting::all();
    }

    public function update($gateway, $data)
    {
        return PaymentSetting::updateOrCreate(
            ['gateway' => $gateway],
            $data
        );
    }
}
