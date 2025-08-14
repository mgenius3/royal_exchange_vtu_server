<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VtuProvider;
use App\Models\VtuPlan;

class VtuSeeder extends Seeder
{
    public function run()
    {
        $providers = [
            ['name' => 'VTpass', 'api_key' => 'key123', 'api_token' => 'token123', 'base_url' => 'https://api.vtpass.com'],
            ['name' => 'Mobilevtu', 'api_key' => 'key456', 'api_token' => 'token456', 'base_url' => 'https://api.mobilevtu.com'],
        ];

        foreach ($providers as $provider) {
            VtuProvider::create($provider);
        }

        $plans = [
            ['vtu_provider_id' => 1, 'network' => 'MTN', 'type' => 'airtime', 'plan_code' => 'MTN-A100', 'description' => 'MTN 100 Naira Airtime', 'price' => 100, 'commission' => 3],
            ['vtu_provider_id' => 1, 'network' => 'Airtel', 'type' => 'data', 'plan_code' => 'AIRTEL-1GB', 'description' => 'Airtel 1GB Data', 'price' => 300, 'commission' => 10],
            ['vtu_provider_id' => 2, 'network' => 'Glo', 'type' => 'tv', 'plan_code' => 'GLO-DSTV', 'description' => 'DSTV Premium', 'price' => 5000, 'commission' => 50],
            ['vtu_provider_id' => 2, 'network' => null, 'type' => 'electricity', 'plan_code' => 'PHED-500', 'description' => 'PHED 500 Naira', 'price' => 500, 'commission' => 5],
        ];

        foreach ($plans as $plan) {
            VtuPlan::create($plan);
        }
    }
}