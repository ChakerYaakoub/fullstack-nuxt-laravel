<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\QuoteRequest;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'products' => Product::query()->count(),
                'categories' => Category::query()->count(),
                'quotes' => QuoteRequest::query()->count(),
                'quotesReceived' => QuoteRequest::query()->where('status', 'received')->count(),
            ],
        ]);
    }
}
