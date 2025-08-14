<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display all wallet transactions in the admin dashboard.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 20); // Default to 20 items per page
            $transactions = WalletTransaction::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return view('transactions.wallet', compact('transactions'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch wallet transactions for admin', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to load transactions.');
        }
    }

    /**
     * Delete a wallet transaction.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $transaction = WalletTransaction::findOrFail($id);
            $transaction->delete();

            return redirect()->route('admin.wallet-transactions.index')
                ->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete wallet transaction', [
                'transaction_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }
}