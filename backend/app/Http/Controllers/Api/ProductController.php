<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()->with('category')->orderBy('id');

        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }

        return response()->json([
            'data' => $query->get()->map->toApiArray()->values(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::query()->with('category')->where('slug', $slug)->firstOrFail();

        return response()->json([
            'data' => $product->toApiArray(),
        ]);
    }
}
