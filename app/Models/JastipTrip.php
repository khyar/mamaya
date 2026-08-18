<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JastipTrip extends Model
{
    protected $fillable = [
        'destination',
        'slug',
        'departure_date',
        'return_date',
        'po_close_date',
        'baggage_quota_kg',
        'description',
        'is_active',
    ];

    protected $casts = [
        'departure_date' => 'datetime',
        'return_date' => 'datetime',
        'po_close_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function catalogs(): HasMany
    {
        return $this->hasMany(JastipCatalog::class, 'trip_id');
    }
}
