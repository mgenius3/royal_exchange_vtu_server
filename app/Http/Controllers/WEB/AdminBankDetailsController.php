<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Services\BankDetailsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminBankDetailsController extends Controller
{
    protected $bankDetailsService;

    public function __construct(BankDetailsService $bankDetailsService)
    {
        $this->bankDetailsService = $bankDetailsService;
    }

    // Display the bank details page
    public function index()
    {
        $bankDetailsList = $this->bankDetailsService->getBankDetails();
        return view('bank_details.index', compact('bankDetailsList'));
    }

    // Store a new bank account
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:50'
        ]);

        $this->bankDetailsService->createBankDetails($request->all(), Auth::id());
        return redirect()->back()->with('success', 'Bank account added successfully');
    }

    // Delete a bank account
    public function delete($id)
    {
        try {
            $this->bankDetailsService->deleteBankDetails($id, Auth::id());
            return redirect()->back()->with('success', 'Bank account deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete bank account: ' . $e->getMessage());
        }
    }
}