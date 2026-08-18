<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'title',
        'description',
        'open_date',
        'close_date',
        'delivery_date',
        'ready_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'open_date' => 'datetime',
            'close_date' => 'datetime',
            'delivery_date' => 'date',
            'ready_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function promos(): HasMany
    {
        return $this->hasMany(Promo::class);
    }

    /**
     * Check if this batch is currently open for ordering.
     */
    public function isOpen(): bool
    {
        $now = now();
        return $this->is_active
            && $now->gte($this->open_date)
            && $now->lte($this->close_date);
    }

    /**
     * Scope: only active batches whose ordering window is open now.
     */
    public function scopeOpen($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where('open_date', '<=', $now)
            ->where('close_date', '>=', $now);
    }

    /**
     * Scope: active batches (regardless of date window).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
