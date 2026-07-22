<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return $this->respond($request);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'productId' => ['required'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ]);

        $product = Product::query()->findOrFail($data['productId']);
        $sessionId = $this->sessionId($request);
        $qty = (int) ($data['quantity'] ?? 1);

        $item = CartItem::query()->firstOrNew([
            'session_id' => $sessionId,
            'product_id' => $product->id,
        ]);

        $item->quantity = ($item->exists ? $item->quantity : 0) + $qty;
        $item->save();

        return $this->respond($request, $sessionId);
    }

    public function update(Request $request, string $productId): JsonResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer'],
        ]);

        $sessionId = $this->sessionId($request);
        $item = CartItem::query()
            ->where('session_id', $sessionId)
            ->where('product_id', $productId)
            ->firstOrFail();

        if ($data['quantity'] <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $data['quantity']]);
        }

        return $this->respond($request, $sessionId);
    }

    public function destroy(Request $request, string $productId): JsonResponse
    {
        $sessionId = $this->sessionId($request);

        CartItem::query()
            ->where('session_id', $sessionId)
            ->where('product_id', $productId)
            ->delete();

        return $this->respond($request, $sessionId);
    }

    public function clear(Request $request): JsonResponse
    {
        $sessionId = $this->sessionId($request);

        CartItem::query()
            ->where('session_id', $sessionId)
            ->delete();

        return $this->respond($request, $sessionId);
    }

    private function respond(Request $request, ?string $sessionId = null): JsonResponse
    {
        $sessionId ??= $this->sessionId($request);

        return response()
            ->json($this->cartPayload($sessionId))
            ->header('X-Cart-Session', $sessionId);
    }

    private function sessionId(Request $request): string
    {
        $fromHeader = $request->header('X-Cart-Session');
        if (is_string($fromHeader) && $fromHeader !== '') {
            return $fromHeader;
        }

        return (string) Str::uuid();
    }

    private function cartPayload(string $sessionId): array
    {
        $items = CartItem::query()
            ->with('product.category')
            ->where('session_id', $sessionId)
            ->get()
            ->filter(fn (CartItem $item) => $item->product !== null)
            ->map(fn (CartItem $item) => [
                'product' => $item->product->toApiArray(),
                'quantity' => $item->quantity,
            ])
            ->values();

        return [
            'items' => $items,
            'count' => $items->sum('quantity'),
            'total' => $items->sum(fn ($row) => $row['product']['price'] * $row['quantity']),
        ];
    }
}
