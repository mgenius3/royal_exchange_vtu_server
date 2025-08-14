<?php

namespace App\Services;

use App\Models\VtuProvider;
use App\Models\VtuPlan;
use App\Models\VtuTransaction;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class VtuService
{
    public function createProvider($data, $userId)
    {
        $provider = VtuProvider::create($data);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'vtu_provider_created',
            'details' => json_encode($data),
            'created_at' => now(),
        ]);

        return $provider;
    }

    public function updateProvider($providerId, $data, $userId)
    {
        $provider = VtuProvider::findOrFail($providerId);
        $oldData = $provider->toArray();
        $provider->update($data);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'vtu_provider_updated',
            'details' => json_encode(['old' => $oldData, 'new' => $data]),
            'created_at' => now(),
        ]);

        return $provider;
    }

    public function createPlan($data, $userId)
    {
        $plan = VtuPlan::create($data);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'vtu_plan_created',
            'details' => json_encode($data),
            'created_at' => now(),
        ]);

        return $plan;
    }

    public function updatePlan($planId, $data, $userId)
    {
        $plan = VtuPlan::findOrFail($planId);
        $oldData = $plan->toArray();
        $plan->update($data);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'vtu_plan_updated',
            'details' => json_encode(['old' => $oldData, 'new' => $data]),
            'created_at' => now(),
        ]);

        return $plan;
    }

    public function processTransaction($data, $userId)
    {
        $plan = VtuPlan::findOrFail($data['vtu_plan_id']);
        $provider = VtuProvider::findOrFail($data['vtu_provider_id']);
        $data['amount'] = $plan->price;

        // Simulate API call (replace with real API integration)
        $response = $this->simulateApiCall($provider, $plan, $data);

        $transaction = VtuTransaction::create([
            'user_id' => $data['user_id'],
            'vtu_plan_id' => $data['vtu_plan_id'],
            'vtu_provider_id' => $data['vtu_provider_id'],
            'phone_number' => $data['phone_number'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'amount' => $data['amount'],
            'status' => $response['status'],
            'transaction_id' => $response['transaction_id'],
            'response_message' => $response['message'],
        ]);

        $this->updateProviderSuccessRate($provider, $response['status']);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'vtu_transaction_processed',
            'details' => json_encode($data + $response),
            'created_at' => now(),
        ]);

        return $transaction;
    }

    public function refundTransaction($transactionId, $userId)
    {
        $transaction = VtuTransaction::findOrFail($transactionId);
        if ($transaction->is_refunded) {
            throw new \Exception('Transaction already refunded');
        }

        $transaction->update(['is_refunded' => true, 'status' => 'refunded']);

        // Logic to credit user wallet (simplified)
        // User::find($transaction->user_id)->increment('wallet_balance', $transaction->amount);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'vtu_transaction_refunded',
            'details' => json_encode(['transaction_id' => $transactionId]),
            'created_at' => now(),
        ]);

        return $transaction;
    }

    private function simulateApiCall($provider, $plan, $data)
    {
        // Replace with actual API call to VTU provider (e.g., VTpass, Mobilevtu)
        return [
            'status' => rand(0, 10) > 1 ? 'success' : 'failed', // 90% success rate for simulation
            'transaction_id' => 'VTU' . time(),
            'message' => 'Transaction processed successfully',
        ];
    }

    private function updateProviderSuccessRate($provider, $status)
    {
        $total = $provider->transactions()->count();
        $success = $provider->transactions()->where('status', 'success')->count();
        $newSuccessRate = $total > 0 ? ($success / $total) * 100 : 100;
        $provider->update(['success_rate' => round($newSuccessRate)]);
    }

    public function getAllTransactions($filters = [])
    {
        $query = VtuTransaction::with(['user', 'plan', 'provider']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->latest()->get();
    }
}