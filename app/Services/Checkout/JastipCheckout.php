<?php

namespace App\Services\Checkout;

use App\Models\JastipTrip;
use App\Models\Order;
use App\Services\UniqueBookingCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JastipCheckout implements CheckoutStrategyInterface
{
    protected $bookingService;

    public function __construct(UniqueBookingCodeService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function validate(Request $request): array
    {
        return $request->validate([
            'trip_id' => 'required|exists:jastip_trips,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'special_requests' => 'required|string', // Contains URL or description
        ]);
    }

    public function process(array $data): Order
    {
        $trip = JastipTrip::findOrFail($data['trip_id']);

        if (!$trip->is_active || now()->isAfter($trip->po_close_date)) {
            throw ValidationException::withMessages([
                'trip_id' => 'Maaf, PO Jastip untuk trip ini sudah ditutup.',
            ]);
        }

        return DB::transaction(function () use ($data, $trip) {
            // For custom requests, the price is initially 0. Admin will provide a quotation later.
            $subtotal = 0;

            // Create Master Order
            $order = Order::create([
                'order_number' => $this->bookingService->generateOrderNumber('JST'),
                'order_type' => 'jastip',
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'grand_total' => null, // Waiting for quotation
                'status' => 'pending', // Special status "pending" means awaiting admin quotation
            ]);

            // No order_items yet until Admin quotes.
            // Or we can create a dummy order item to represent the request.
            // Let's create an item with price 0.
            $order->items()->create([
                'sellable_type' => JastipTrip::class, // Poly to trip directly since no specific catalog item
                'sellable_id' => $trip->id,
                'product_name' => 'Custom Request: ' . substr($data['special_requests'], 0, 50) . '...',
                'product_price' => 0,
                'quantity' => 1,
                'subtotal' => 0,
            ]);

            // Create Jastip Detail
            $order->jastipDetail()->create([
                'trip_id' => $trip->id,
                'shipping_address' => $data['shipping_address'],
                'special_requests' => $data['special_requests'],
                'booking_code' => $this->bookingService->generateJastipBookingCode(),
            ]);

            return $order;
        });
    }
}
