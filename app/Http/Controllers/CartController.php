<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * View cart.
     */
    public function index()
    {
        $cart = session('cart', ['batch_id' => null, 'items' => []]);
        $batch = null;
        $cartItems = [];
        $subtotal = 0;

        if ($cart['batch_id']) {
            $batch = Batch::find($cart['batch_id']);
        }

        foreach ($cart['items'] as $productId => $item) {
            $product = Product::with('images')->find($productId);
            if ($product) {
                $lineTotal = (float) $product->price * $item['quantity'];
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'line_total' => $lineTotal,
                ];
                $subtotal += $lineTotal;
            }
        }

        return view('storefront.cart', compact('batch', 'cartItems', 'subtotal'));
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'batch_id' => ['required', 'integer', 'exists:batches,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $cart = session('cart', ['batch_id' => null, 'items' => []]);

        // If switching batches, clear cart
        if ($cart['batch_id'] && $cart['batch_id'] != $request->batch_id) {
            $cart = ['batch_id' => (int) $request->batch_id, 'items' => []];
        }

        $cart['batch_id'] = (int) $request->batch_id;

        // Verify product belongs to this batch
        $batch = Batch::findOrFail($request->batch_id);
        if (! $batch->products()->where('products.id', $request->product_id)->exists()) {
            return back()->with('error', 'Produk tidak tersedia di batch ini.');
        }

        $productId = (int) $request->product_id;
        if (isset($cart['items'][$productId])) {
            $cart['items'][$productId]['quantity'] += (int) $request->quantity;
        } else {
            $cart['items'][$productId] = [
                'quantity' => (int) $request->quantity,
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    /**
     * Update item quantity.
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $cart = session('cart', ['batch_id' => null, 'items' => []]);
        $productId = (int) $request->product_id;

        if ((int) $request->quantity === 0) {
            unset($cart['items'][$productId]);
        } else {
            if (isset($cart['items'][$productId])) {
                $cart['items'][$productId]['quantity'] = (int) $request->quantity;
            }
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    /**
     * Remove item from cart.
     */
    public function remove(int $productId)
    {
        $cart = session('cart', ['batch_id' => null, 'items' => []]);
        unset($cart['items'][$productId]);

        if (empty($cart['items'])) {
            $cart['batch_id'] = null;
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    /**
     * Clear entire cart.
     */
    public function clear()
    {
        session()->forget('cart');

        return back()->with('success', 'Keranjang dikosongkan.');
    }

    /**
     * Get cart item count (for header badge).
     */
    public static function getCartCount(): int
    {
        $cart = session('cart', ['batch_id' => null, 'items' => []]);
        $count = 0;
        foreach ($cart['items'] as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
}
