<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AdService;

class AdController extends Controller
{
    protected $adService;

    public function __construct(AdService $adService)
    {
        $this->adService = $adService;
    }

    public function index()
    {
        $ads = $this->adService->getActiveAds();
        return response()->json([
            'status' => 'success',
            'data' => $ads
        ]);
    }
}