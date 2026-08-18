<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketOrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'event_id',
        'ktp_number',
        'email_address',
        'booking_code',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(TicketEvent::class);
    }
}
