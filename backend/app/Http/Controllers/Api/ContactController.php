<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\QuoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'category' => ['required', 'string', 'max:60'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $quote = QuoteRequest::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'category_slug' => $data['category'],
            'message' => $data['message'],
            'status' => 'received',
        ]);

        $category = Category::query()->where('slug', $data['category'])->first();

        return response()->json([
            'data' => [
                'id' => (string) $quote->id,
                'status' => $quote->status,
                'category' => $category?->name ?? $data['category'],
                'name' => $quote->name,
                'email' => $quote->email,
            ],
            'message' => 'Quote request saved in Laravel + SQLite.',
        ], 201);
    }
}
