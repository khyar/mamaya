<?php

namespace App\Services;

use App\Models\Promo;

class PromoService
{
    /**
     * Validate a promo code for a given batch and subtotal.
     *
     * @return array ['valid' => bool, 'message' => string, 'promo' => Promo|null, 'discount' => float]
     */
    public function validate(string $code, int $batchId, float $subtotal): array
    {
        $promo = Promo::where('code', strtoupper(trim($code)))->first();

        if (! $promo) {
            return [
                'valid' => false,
                'message' => 'Kode promo tidak ditemukan.',
                'promo' => null,
                'discount' => 0,
            ];
        }

        if (! $promo->is_active) {
            return [
                'valid' => false,
                'message' => 'Kode promo sudah tidak aktif.',
                'promo' => null,
                'discount' => 0,
            ];
        }

        $now = now();

        if ($promo->start_date && $now->lt($promo->start_date)) {
            return [
                'valid' => false,
                'message' => 'Kode promo belum berlaku.',
                'promo' => null,
                'discount' => 0,
            ];
        }

        if ($promo->end_date && $now->gt($promo->end_date)) {
            return [
                'valid' => false,
                'message' => 'Kode promo sudah kedaluwarsa.',
                'promo' => null,
                'discount' => 0,
            ];
        }

        if ($promo->batch_id && $promo->batch_id !== $batchId) {
            return [
                'valid' => false,
                'message' => 'Kode promo tidak berlaku untuk batch ini.',
                'promo' => null,
                'discount' => 0,
            ];
        }

        if ($promo->max_uses && $promo->used_count >= $promo->max_uses) {
            return [
                'valid' => false,
                'message' => 'Kode promo sudah mencapai batas penggunaan.',
                'promo' => null,
                'discount' => 0,
            ];
        }

        if ($promo->min_order && $subtotal < (float) $promo->min_order) {
            $minFormatted = 'Rp ' . number_format((float) $promo->min_order, 0, ',', '.');
            return [
                'valid' => false,
                'message' => "Minimum belanja {$minFormatted} untuk menggunakan kode promo ini.",
                'promo' => null,
                'discount' => 0,
            ];
        }

        $discount = $promo->calculateDiscount($subtotal);

        $discountFormatted = 'Rp ' . number_format($discount, 0, ',', '.');
        return [
            'valid' => true,
            'message' => "Kode promo berhasil diterapkan! Diskon: {$discountFormatted}",
            'promo' => $promo,
            'discount' => $discount,
        ];
    }
}
