<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->with('category')
            ->orderByDesc('id')
            ->get()
            ->map->toApiArray()
            ->values();

        return response()->json(['data' => $products]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $product = Product::query()->create($data)->load('category');

        return response()->json(['data' => $product->toApiArray()], 201);
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::query()->with('category')->findOrFail($id);

        return response()->json(['data' => $product->toApiArray()]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::query()->findOrFail($id);
        $product->update($this->validated($request, $product->id));
        $product->load('category');

        return response()->json(['data' => $product->toApiArray()]);
    }

    public function destroy(string $id): JsonResponse
    {
        Product::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Product deleted']);
    }

    private function validated(Request $request, ?int $productId = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'categoryId' => ['required', 'integer', Rule::exists('categories', 'id')],
            'slug' => [
                'nullable',
                'string',
                'max:190',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'price' => ['required', 'integer', 'min:0'],
            'shortDescription' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'material' => ['required', 'string', 'max:120'],
            'finish' => ['required', 'string', 'max:120'],
            'dimensions' => ['required', 'string', 'max:120'],
            'featured' => ['sometimes', 'boolean'],
            'imageTone' => ['nullable', 'string', 'max:20'],
        ]);

        return [
            'category_id' => $validated['categoryId'],
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'price' => $validated['price'],
            'short_description' => $validated['shortDescription'],
            'description' => $validated['description'],
            'material' => $validated['material'],
            'finish' => $validated['finish'],
            'dimensions' => $validated['dimensions'],
            'featured' => (bool) ($validated['featured'] ?? false),
            'image_tone' => $validated['imageTone'] ?? '#2c3538',
        ];
    }
}
