<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketEvent extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'venue',
        'war_start_time',
        'war_end_time',
        'event_date',
        'banner_image',
        'terms',
        'is_active',
    ];

    protected $casts = [
        'war_start_time' => 'datetime',
        'war_end_time' => 'datetime',
        'event_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(TicketCategory::class, 'event_id');
    }

    public function isWarActive(): bool
    {
        $now = now();
        return $this->is_active && $now->between($this->war_start_time, $this->war_end_time);
    }
}
