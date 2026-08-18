<?php

namespace App\Services;

use App\Models\Order;
use App\Models\JastipOrderDetail;
use App\Models\TicketOrderDetail;
use Illuminate\Support\Str;

class UniqueBookingCodeService
{
    /**
     * Generate a unique order number.
     */
    public function generateOrderNumber(string $prefix = 'ORD'): string
    {
        do {
            $number = $prefix . '-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Generate a unique booking code for tickets.
     */
    public function generateTicketBookingCode(): string
    {
        do {
            $code = 'TIX-' . strtoupper(Str::random(8));
        } while (TicketOrderDetail::where('booking_code', $code)->exists());

        return $code;
    }

    /**
     * Generate a unique booking code for Jastip.
     */
    public function generateJastipBookingCode(): string
    {
        do {
            $code = 'JST-' . strtoupper(Str::random(8));
        } while (JastipOrderDetail::where('booking_code', $code)->exists());

        return $code;
    }
}
