<?php


namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Services\VtuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VtuProvider;
use App\Models\VtuPlan;
use App\Models\User;
use App\Models\VtuTransaction;


class AdminVtuController extends Controller
{
    protected $vtuService;

    public function __construct(VtuService $vtuService)
    {
        $this->vtuService = $vtuService;
    }

    public function adminPage()
    {
        $providers = VtuProvider::all();
        $plans = VtuPlan::with('provider')->get();
        $transactions = $this->vtuService->getAllTransactions()->take(5);
        $stats = [
            'total_transactions' => VtuTransaction::count(),
            'success_rate' => VtuTransaction::where('status', 'success')->count() / max(VtuTransaction::count(), 1) * 100,
            'revenue' => VtuTransaction::where('status', 'success')->sum('amount'),
        ];
        return view('vtu_management.display', compact('providers', 'plans', 'transactions', 'stats'));
    }

    public function storeProvider(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vtu_providers',
            'api_key' => 'nullable|string|max:255',
            'api_token' => 'nullable|string|max:255',
            'base_url' => 'nullable|url'
        ]);

        $this->vtuService->createProvider($request->all(), Auth::id());
        return redirect()->back()->with('success', 'VTU Provider added successfully');
    }

    public function updateProvider(Request $request, $providerId)
    {
        $request->validate([
            'api_key' => 'nullable|string|max:255',
            'api_token' => 'nullable|string|max:255',
            'base_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $this->vtuService->updateProvider($providerId, $request->all(), Auth::id());
        return redirect()->back()->with('success', 'Provider updated successfully');
    }

    public function storePlan(Request $request)
    {
        $request->validate([
            'vtu_provider_id' => 'required|exists:vtu_providers,id',
            'network' => 'nullable|string|max:50',
            'type' => 'required|in:airtime,data,tv,electricity',
            'plan_code' => 'required|string|max:50|unique:vtu_plans',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
        ]);

        $this->vtuService->createPlan($request->all(), Auth::id());
        return redirect()->back()->with('success', 'VTU Plan added successfully');
    }

    public function createTransaction()
    {
        $plans = VtuPlan::where('is_active', true)->with('provider')->get();
        $users = User::all();
        return view('vtu_management.create_transaction', compact('plans', 'users'));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vtu_plan_id' => 'required|exists:vtu_plans,id',
            'phone_number' => 'nullable|string|max:15|required_if:type,airtime,data',
            'account_number' => 'nullable|string|max:20|required_if:type,tv,electricity',
        ]);

        $plan = VtuPlan::findOrFail($request->vtu_plan_id);
        $data = $request->all();
        $data['vtu_provider_id'] = $plan->vtu_provider_id;

        $this->vtuService->processTransaction($data, Auth::id());
        return redirect()->route('admin.vtu')->with('success', 'Transaction processed successfully');
    }

    public function allTransactions()
    {
        $transactions = $this->vtuService->getAllTransactions();
        return view('vtu_management.all_transaction', compact('transactions'));
    }

    public function refundTransaction($transactionId)
    {
        $this->vtuService->refundTransaction($transactionId, Auth::id());
        return redirect()->back()->with('success', 'Transaction refunded successfully');
    }
}