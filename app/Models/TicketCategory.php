<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TicketCategory extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quota',
        'available_quota',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(TicketEvent::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'sellable');
    }
}
