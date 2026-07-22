<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Category::query()
                ->orderBy('id')
                ->get()
                ->map->toApiArray()
                ->values(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $category = Category::query()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'data' => $category->toApiArray(),
        ]);
    }
}
