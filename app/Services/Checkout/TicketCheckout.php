<?php

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\TicketCategory;
use App\Models\TicketEvent;
use App\Services\UniqueBookingCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketCheckout implements CheckoutStrategyInterface
{
    protected $bookingService;

    public function __construct(UniqueBookingCodeService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function validate(Request $request): array
    {
        return $request->validate([
            'event_id' => 'required|exists:ticket_events,id',
            'category_id' => 'required|exists:ticket_categories,id',
            'quantity' => 'required|integer|min:1|max:4', // Max 4 tickets per transaction
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'email_address' => 'required|email|max:255',
            'ktp_number' => 'required|string|size:16',
        ]);
    }

    public function process(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $event = TicketEvent::findOrFail($data['event_id']);
            
            if (!$event->isWarActive()) {
                throw ValidationException::withMessages([
                    'event_id' => 'Penjualan tiket untuk event ini sedang ditutup.',
                ]);
            }

            // PESSIMISTIC LOCKING: Lock the row for update so no one else can read/modify it until transaction commits.
            $category = TicketCategory::where('id', $data['category_id'])
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($category->available_quota < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Maaf, kuota tiket tidak mencukupi. Sisa: ' . $category->available_quota,
                ]);
            }

            // Deduct quota immediately
            $category->decrement('available_quota', $data['quantity']);

            $subtotal = $category->price * $data['quantity'];

            // Create Master Order
            $order = Order::create([
                'order_number' => $this->bookingService->generateOrderNumber('TIX'),
                'order_type' => 'ticket',
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'subtotal' => $subtotal,
                'discount_amount' => 0, // No promos for tickets usually
                'grand_total' => $subtotal,
                'status' => 'awaiting_payment',
            ]);

            // Create Order Item
            $order->items()->create([
                'sellable_type' => TicketCategory::class,
                'sellable_id' => $category->id,
                'product_name' => $event->name . ' - ' . $category->name,
                'product_price' => $category->price,
                'quantity' => $data['quantity'],
                'subtotal' => $subtotal,
            ]);

            // Create Ticket Detail
            $order->ticketDetail()->create([
                'event_id' => $event->id,
                'ktp_number' => $data['ktp_number'],
                'email_address' => $data['email_address'],
                'booking_code' => $this->bookingService->generateTicketBookingCode(),
            ]);

            return $order;
        });
    }
}
