<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create an admin user
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'phone' => '1234567890',
            'wallet_balance' => 1000.00,
            'is_verified' => true,
            'is_admin' => true,
            'date_joined' => Carbon::now(),
            'last_login' => Carbon::now(),
            'referral_code' => Str::random(10),
            'wallet_addresses' => json_encode(['btc' => '1A1zP1...', 'eth' => '0xabc123...']),
            'password' => Hash::make('123456789'), // Securely hash the password
            'remember_token' => Str::random(10),
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}