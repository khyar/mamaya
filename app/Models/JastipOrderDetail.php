<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JastipOrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'trip_id',
        'shipping_address',
        'special_requests',
        'booking_code',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(JastipTrip::class);
    }
}
