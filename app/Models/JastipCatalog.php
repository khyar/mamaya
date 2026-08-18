<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class JastipCatalog extends Model
{
    protected $fillable = [
        'trip_id',
        'name',
        'estimated_price',
        'reference_url',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(JastipTrip::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'sellable');
    }
}
