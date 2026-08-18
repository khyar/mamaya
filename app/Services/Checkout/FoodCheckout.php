<?php

namespace App\Services\Checkout;

use App\Models\Batch;
use App\Models\Order;
use App\Models\Product;
use App\Services\PromoService;
use App\Services\UniqueBookingCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FoodCheckout implements CheckoutStrategyInterface
{
    protected $promoService;
    protected $bookingService;

    public function __construct(PromoService $promoService, UniqueBookingCodeService $bookingService)
    {
        $this->promoService = $promoService;
        $this->bookingService = $bookingService;
    }

    public function validate(Request $request): array
    {
        return $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_method' => 'required|in:pickup,delivery',
            'customer_address' => 'required_if:shipping_method,delivery|nullable|string',
            'notes' => 'nullable|string',
            'promo_code' => 'nullable|string|exists:promos,code',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
    }

    public function process(array $data): Order
    {
        $batch = Batch::findOrFail($data['batch_id']);
        
        if (!$batch->isOpen()) {
            throw ValidationException::withMessages([
                'batch_id' => 'Maaf, PO untuk batch ini sudah ditutup.',
            ]);
        }

        return DB::transaction(function () use ($data, $batch) {
            $subtotal = 0;
            $orderItemsData = [];

            // Calculate subtotal and build items
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Verify product belongs to this batch
                if (!$batch->products->contains($product->id)) {
                    throw ValidationException::withMessages([
                        'items' => "Produk {$product->name} tidak tersedia di batch ini.",
                    ]);
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItemsData[] = [
                    'sellable_type' => Product::class,
                    'sellable_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Apply Promo
            $discountAmount = 0;
            $promoId = null;
            $promoCodeUsed = null;

            if (!empty($data['promo_code'])) {
                $promoResult = $this->promoService->calculateDiscount($data['promo_code'], $subtotal, $batch->id);
                if ($promoResult['is_valid']) {
                    $discountAmount = $promoResult['discount_amount'];
                    $promoId = $promoResult['promo_id'];
                    $promoCodeUsed = $data['promo_code'];
                }
            }

            // Create Master Order
            $order = Order::create([
                'order_number' => $this->bookingService->generateOrderNumber('FOD'),
                'order_type' => 'food',
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'promo_id' => $promoId,
                'promo_code_used' => $promoCodeUsed,
                'grand_total' => $subtotal - $discountAmount, // Shipping cost added later if delivery
                'status' => $data['shipping_method'] === 'delivery' ? 'awaiting_shipping_cost' : 'awaiting_payment',
            ]);

            // Create Order Items
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // Create Food Specific Detail
            $order->foodDetail()->create([
                'batch_id' => $batch->id,
                'shipping_method' => $data['shipping_method'],
                'customer_address' => $data['shipping_method'] === 'delivery' ? $data['customer_address'] : null,
                'notes' => $data['notes'] ?? null,
            ]);

            return $order;
        });
    }
}
