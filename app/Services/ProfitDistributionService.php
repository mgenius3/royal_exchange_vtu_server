<?php

namespace App\Services;

use App\Models\ProfitWallet;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class ProfitDistributionService
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function distributeProfit(float $profit)
    {
        return DB::transaction(function () use ($profit) {
            // Calculate developer and admin profit
            $developerProfit = $profit * 0.07; // 7% for developer
            $adminProfit = $profit * 0.93; // 93% for admin

            // Update developer wallet
            $developerWallet = ProfitWallet::where('type', 'developer')->first();
            $developerWallet->updateBalance($developerProfit);

            // Update admin wallet
            $adminWallet = ProfitWallet::where('type', 'admin')->first();
            $adminWallet->updateBalance($adminProfit);

            return [
                'developer_profit' => $developerProfit,
                'admin_profit' => $adminProfit,
            ];
        });
    }

    public function withdrawProfit(string $walletType, float $amount)
    {
        if (!in_array($walletType, ['admin', 'developer'])) {
            throw new InvalidArgumentException('Invalid wallet type. Use "admin" or "developer".');
        }

        if ($amount <= 0) {
            throw new InvalidArgumentException('Withdrawal amount must be greater than zero.');
        }

        return DB::transaction(function () use ($walletType, $amount) {
            // Fetch the wallet
            $wallet = ProfitWallet::where('type', $walletType)->first();

            if (!$wallet) {
                throw new RuntimeException("Wallet of type {$walletType} not found.");
            }

            // Check for sufficient balance
            if ($wallet->balance < $amount) {
                throw new RuntimeException("Insufficient balance in {$walletType} wallet. Available: {$wallet->balance}, Requested: {$amount}.");
            }

            // Update wallet balance (subtract amount)
            $wallet->updateBalance(-$amount);

            return [
                'wallet_type' => $walletType,
                'withdrawn_amount' => $amount,
                'new_balance' => $wallet->balance,
            ];
        });
    }

    public function transferToUser(string $walletType, int $userId, float $amount)
    {
        if (!in_array($walletType, ['admin', 'developer'])) {
            throw new InvalidArgumentException('Invalid wallet type. Use "admin" or "developer".');
        }

        if ($amount <= 0) {
            throw new InvalidArgumentException('Transfer amount must be greater than zero.');
        }

        return DB::transaction(function () use ($walletType, $userId, $amount) {
            // Fetch the wallet
            $wallet = ProfitWallet::where('type', $walletType)->first();

            if (!$wallet) {
                throw new RuntimeException("Wallet of type {$walletType} not found.");
            }

            // Check for sufficient balance
            if ($wallet->balance < $amount) {
                throw new RuntimeException("Insufficient balance in {$walletType} wallet. Available: {$wallet->balance}, Requested: {$amount}.");
            }

            // Update profit wallet balance (subtract amount)
            $wallet->updateBalance(-$amount);

            // Fund user wallet
            $user = $this->userService->fundWallet($userId, $amount);

            if (!$user) {
                throw new RuntimeException("User with ID {$userId} not found.");
            }

            return [
                'wallet_type' => $walletType,
                'user_id' => $userId,
                'transferred_amount' => $amount,
                'new_wallet_balance' => $wallet->balance,
                'new_user_balance' => $user->wallet_balance
            ];
        });
    }
}