<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Generate a unique order number: DM-YYYYMMDD-XXXX
     */
    public function generateOrderNumber(): string
    {
        $prefix = 'DM-' . now()->format('Ymd') . '-';
        $randomPart = strtoupper(Str::random(4));
        $orderNumber = $prefix . $randomPart;

        // Ensure uniqueness
        while (Order::where('order_number', $orderNumber)->exists()) {
            $randomPart = strtoupper(Str::random(4));
            $orderNumber = $prefix . $randomPart;
        }

        return $orderNumber;
    }

    /**
     * Create an order from the cart session and checkout data.
     *
     * @param array $cartItems  ['product_id' => ['quantity' => int, ...], ...]
     * @param int   $batchId
     * @param array $customerData  ['name', 'phone', 'address', 'shipping_method', 'notes']
     * @param Promo|null $promo
     * @return Order
     */
    public function createOrder(array $cartItems, int $batchId, array $customerData, ?Promo $promo = null): Order
    {
        return DB::transaction(function () use ($cartItems, $batchId, $customerData, $promo) {
            // Calculate subtotal from fresh product prices
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($cartItems as $productId => $item) {
                $product = Product::findOrFail($productId);
                $qty = (int) $item['quantity'];
                $lineSubtotal = (float) $product->price * $qty;
                $subtotal += $lineSubtotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $qty,
                    'subtotal' => $lineSubtotal,
                ];
            }

            // Calculate discount
            $discountAmount = 0;
            if ($promo && $promo->isValid($batchId, $subtotal)) {
                $discountAmount = $promo->calculateDiscount($subtotal);
            }

            // Determine status & grand total based on shipping method
            $shippingMethod = $customerData['shipping_method'];
            if ($shippingMethod === 'pickup') {
                $shippingCost = 0;
                $grandTotal = $subtotal - $discountAmount;
                $status = 'awaiting_payment';
            } else {
                $shippingCost = null;
                $grandTotal = null;
                $status = 'awaiting_shipping_cost';
            }

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'batch_id' => $batchId,
                'customer_name' => $customerData['name'],
                'customer_phone' => $customerData['phone'],
                'customer_address' => $customerData['address'] ?? null,
                'shipping_method' => $shippingMethod,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'promo_id' => $promo?->id,
                'promo_code_used' => $promo?->code,
                'shipping_cost' => $shippingCost,
                'grand_total' => $grandTotal,
                'status' => $status,
                'notes' => $customerData['notes'] ?? null,
            ]);

            // Create order items
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // Increment promo used count
            if ($promo) {
                $promo->increment('used_count');
            }

            return $order;
        });
    }

    /**
     * Set shipping cost for a delivery order and generate payment link.
     */
    public function setShippingCost(Order $order, float $shippingCost): Order
    {
        $afterDiscount = (float) $order->subtotal - (float) $order->discount_amount;
        $grandTotal = $afterDiscount + $shippingCost;

        $order->update([
            'shipping_cost' => $shippingCost,
            'grand_total' => $grandTotal,
            'status' => 'awaiting_payment',
        ]);

        return $order->fresh();
    }
}
