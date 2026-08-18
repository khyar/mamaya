<?php

namespace App\Services;

use App\Models\Order;

class WhatsAppService
{
    /**
     * Generate a WhatsApp payment link for an order.
     * The message includes order details and bank transfer info.
     */
    public function generatePaymentLink(Order $order): string
    {
        $phone = config('app.whatsapp_number', '6281234567890');
        $bankInfo = config('app.bank_account_info', 'BCA - 1234567890 a/n Dapur Mamaya');

        $order->load('items');

        $items = '';
        foreach ($order->items as $item) {
            $items .= "• {$item->product_name} x{$item->quantity} = Rp " . number_format((float) $item->subtotal, 0, ',', '.') . "\n";
        }

        $message = "🛒 *PESANAN DAPUR MAMAYA*\n\n";
        $message .= "📋 *No. Order:* {$order->order_number}\n";
        $message .= "👤 *Nama:* {$order->customer_name}\n";
        $message .= "📱 *HP:* {$order->customer_phone}\n";
        $message .= "🚚 *Pengiriman:* " . ($order->shipping_method === 'pickup' ? 'Ambil Sendiri' : 'Delivery') . "\n";

        if ($order->shipping_method === 'delivery' && $order->customer_address) {
            $message .= "📍 *Alamat:* {$order->customer_address}\n";
        }

        $message .= "\n📦 *Detail Pesanan:*\n";
        $message .= $items;
        $message .= "\n💰 *Subtotal:* Rp " . number_format((float) $order->subtotal, 0, ',', '.') . "\n";

        if ((float) $order->discount_amount > 0) {
            $message .= "🏷️ *Diskon ({$order->promo_code_used}):* -Rp " . number_format((float) $order->discount_amount, 0, ',', '.') . "\n";
        }

        if ($order->shipping_cost !== null) {
            $message .= "🚚 *Ongkir:* Rp " . number_format((float) $order->shipping_cost, 0, ',', '.') . "\n";
        }

        $message .= "\n✅ *TOTAL: Rp " . number_format((float) $order->grand_total, 0, ',', '.') . "*\n";
        $message .= "\n💳 *Transfer ke:*\n{$bankInfo}\n";
        $message .= "\nMohon kirim bukti transfer ke chat ini. Terima kasih! 🙏";

        $encodedMessage = urlencode($message);

        return "https://wa.me/{$phone}?text={$encodedMessage}";
    }
}
