<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\QuoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuoteController extends Controller
{
    public function index(): JsonResponse
    {
        $quotes = QuoteRequest::query()
            ->orderByDesc('id')
            ->get()
            ->map(fn (QuoteRequest $quote) => $this->toArray($quote))
            ->values();

        return response()->json(['data' => $quotes]);
    }

    public function show(string $id): JsonResponse
    {
        $quote = QuoteRequest::query()->findOrFail($id);

        return response()->json(['data' => $this->toArray($quote)]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $quote = QuoteRequest::query()->findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(['received', 'in_progress', 'done', 'rejected'])],
        ]);

        $quote->update($data);

        return response()->json(['data' => $this->toArray($quote->fresh())]);
    }

    public function destroy(string $id): JsonResponse
    {
        QuoteRequest::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Quote deleted']);
    }

    private function toArray(QuoteRequest $quote): array
    {
        $category = Category::query()->where('slug', $quote->category_slug)->first();

        return [
            'id' => (string) $quote->id,
            'name' => $quote->name,
            'email' => $quote->email,
            'phone' => $quote->phone,
            'categorySlug' => $quote->category_slug,
            'categoryName' => $category?->name ?? $quote->category_slug,
            'message' => $quote->message,
            'status' => $quote->status,
            'createdAt' => $quote->created_at?->toIso8601String(),
        ];
    }
}
