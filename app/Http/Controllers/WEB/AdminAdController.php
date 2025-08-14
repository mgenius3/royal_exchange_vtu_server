<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Services\AdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ad;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Log as FacadesLog;

class AdminAdController extends Controller
{
    protected $adService;

    public function __construct(AdService $adService)
    {
        $this->adService = $adService;
    }

    public function index()
    {
        $ads = Ad::all();
        return view('ads_management.display', compact('ads'));
    }

    public function create()
    {
        return view('ads_management.create_ad');
    }

    

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'image|mimes:jpg,png,jpeg|max:2048',
            'target_url' => 'nullable|url',
            'type' => 'required|in:banner,popup,interstitial',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|integer|min:0'
        ]);


        $this->adService->createAd($request->all(), Auth::id());
        return redirect()->route('admin.ads.index')->with('success', 'Ad created successfully');
    }

    public function edit($adId)
    {
        $ad = Ad::findOrFail($adId);
        return view('ads_management.edit_ad', compact('ad'));
    }

    public function update(Request $request, $adId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'target_url' => 'nullable|url',
            'type' => 'required|in:banner,popup,interstitial',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|integer|min:0',
        ]);

        $this->adService->updateAd($adId, $request->all(), Auth::id());
        return redirect()->route('admin.ads.index')->with('success', 'Ad updated successfully');
    }

    public function destroy($adId)
    {
        $this->adService->deleteAd($adId, Auth::id());
        return redirect()->route('admin.ads.index')->with('success', 'Ad deleted successfully');
    }
}