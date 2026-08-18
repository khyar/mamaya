<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\PromoService;
use App\Services\WhatsAppService;
use App\Services\Checkout\FoodCheckout;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private PromoService $promoService,
        private WhatsAppService $whatsAppService,
    ) {}

    /**
     * Show checkout page.
     */
    public function show()
    {
        $cart = session('cart', ['batch_id' => null, 'items' => []]);

        if (empty($cart['items'])) {
            return redirect()->route('food.cart.index')->with('error', 'Keranjang kosong.');
        }

        $batch = Batch::findOrFail($cart['batch_id']);

        if (! $batch->isOpen()) {
            return redirect()->route('food.cart.index')
                ->with('error', 'Batch ini sudah ditutup. Silakan pilih batch lain.');
        }

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart['items'] as $productId => $item) {
            $product = Product::find($productId);
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

        return view('storefront.checkout', compact('batch', 'cartItems', 'subtotal'));
    }

    /**
     * Validate promo code via AJAX.
     */
    public function validatePromo(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'batch_id' => ['required', 'integer', 'exists:batches,id'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        $result = $this->promoService->validate(
            $request->code,
            (int) $request->batch_id,
            (float) $request->subtotal,
        );

        return response()->json([
            'valid' => $result['valid'],
            'message' => $result['message'],
            'discount' => $result['discount'],
        ]);
    }

    /**
     * Process the checkout.
     */
    public function process(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s\(\)]+$/'],
            'address' => ['required_if:shipping_method,delivery', 'nullable', 'string', 'max:500'],
            'shipping_method' => ['required', 'in:pickup,delivery'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $cart = session('cart', ['batch_id' => null, 'items' => []]);

        if (empty($cart['items'])) {
            return redirect()->route('food.cart.index')->with('error', 'Keranjang kosong.');
        }

        $batch = Batch::findOrFail($cart['batch_id']);

        if (! $batch->isOpen()) {
            return redirect()->route('food.cart.index')
                ->with('error', 'Batch ini sudah ditutup.');
        }

        // Validate promo if provided
        $promo = null;
        if ($request->promo_code) {
            $subtotal = 0;
            foreach ($cart['items'] as $productId => $item) {
                $product = Product::findOrFail($productId);
                $subtotal += (float) $product->price * $item['quantity'];
            }

            $promoResult = $this->promoService->validate(
                $request->promo_code,
                $batch->id,
                $subtotal,
            );

            if ($promoResult['valid']) {
                $promo = $promoResult['promo'];
            }
            // If promo is invalid, silently ignore (already shown in UI)
        }

        // Build items array for FoodCheckout
        $itemsData = [];
        foreach ($cart['items'] as $productId => $item) {
            $itemsData[] = [
                'product_id' => $productId,
                'quantity' => $item['quantity'],
            ];
        }

        $foodCheckout = app(FoodCheckout::class);
        $request->merge([
            'batch_id' => $batch->id,
            'customer_name' => $request->name,
            'customer_phone' => $request->phone,
            'customer_address' => $request->address,
            'items' => $itemsData,
        ]);
        
        $validatedData = $foodCheckout->validate($request);
        $order = $foodCheckout->process($validatedData);

        // Clear cart
        session()->forget('cart');

        // Generate WhatsApp link if pickup (immediate payment)
        $whatsappLink = null;
        if ($order->canShowPaymentLink()) {
            $whatsappLink = $this->whatsAppService->generatePaymentLink($order);
        }

        return redirect()->route('order.confirmation', $order->order_number)
            ->with('whatsapp_link', $whatsappLink);
    }

    /**
     * Show order confirmation page.
     */
    public function confirmation(string $orderNumber)
    {
        $order = \App\Models\Order::where('order_number', $orderNumber)
            ->with(['items', 'foodDetail.batch', 'ticketDetail.event', 'jastipDetail.trip'])
            ->firstOrFail();

        $whatsappLink = session('whatsapp_link');

        if (! $whatsappLink && $order->canShowPaymentLink()) {
            $whatsappLink = $this->whatsAppService->generatePaymentLink($order);
        }

        return view('storefront.order-confirmation', compact('order', 'whatsappLink'));
    }
}
