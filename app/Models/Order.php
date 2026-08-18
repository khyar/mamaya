<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'order_type',
        'customer_name',
        'customer_phone',
        'subtotal',
        'discount_amount',
        'promo_id',
        'promo_code_used',
        'grand_total',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function foodDetail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FoodOrderDetail::class);
    }

    public function ticketDetail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TicketOrderDetail::class);
    }

    public function jastipDetail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(JastipOrderDetail::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Format currency in IDR.
     */
    public static function formatCurrency(float|string|null $amount): string
    {
        if ($amount === null) {
            return '-';
        }
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return self::formatCurrency($this->subtotal);
    }

    public function getFormattedDiscountAttribute(): string
    {
        return self::formatCurrency($this->discount_amount);
    }

    public function getFormattedShippingCostAttribute(): string
    {
        if ($this->order_type === 'food' && $this->foodDetail) {
            return self::formatCurrency($this->foodDetail->shipping_cost);
        }
        return self::formatCurrency(null);
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return self::formatCurrency($this->grand_total);
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'awaiting_shipping_cost' => 'Menunggu Ongkir',
            'awaiting_payment' => 'Menunggu Pembayaran',
            'processing' => 'Diproses',
            'ready' => 'Siap',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    /**
     * Get status badge CSS class.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'awaiting_shipping_cost' => 'bg-orange-100 text-orange-800',
            'awaiting_payment' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-indigo-100 text-indigo-800',
            'ready' => 'bg-green-100 text-green-800',
            'completed' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Check if this order can show a WhatsApp payment link.
     */
    public function canShowPaymentLink(): bool
    {
        return $this->status === 'awaiting_payment' && $this->grand_total !== null;
    }
}
