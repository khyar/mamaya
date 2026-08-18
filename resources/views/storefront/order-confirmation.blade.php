@extends('layouts.storefront')

@section('title', 'Konfirmasi Order - Mamaya')

@section('content')
<div class="max-w-[800px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    <div class="text-center mb-[48px]">
        <div class="w-[80px] h-[80px] mx-auto mb-[24px] bg-green-50 rounded-full flex items-center justify-center">
            <svg class="w-[40px] h-[40px] text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-[32px] font-bold text-ink mb-[8px] tracking-tight">Pesanan Berhasil!</h1>
        <p class="text-[16px] text-muted">Terima kasih telah memesan di Dapur Mamaya</p>
    </div>

    <div class="bg-canvas border border-hairline rounded-[14px] p-[32px] mb-[32px]">
        <div class="text-center mb-[32px] pb-[32px] border-b border-hairline">
            <p class="text-[14px] text-muted mb-[4px] uppercase tracking-wide font-bold">No. Order</p>
            <p class="text-[28px] font-bold text-ink tracking-widest">{{ $order->order_number }}</p>
        </div>

        <div class="grid grid-cols-2 gap-[24px] mb-[32px]">
            <div>
                <p class="text-[13px] text-muted mb-[4px]">Nama</p>
                <p class="font-medium text-ink text-[15px]">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="text-[13px] text-muted mb-[4px]">No. HP</p>
                <p class="font-medium text-ink text-[15px]">{{ $order->customer_phone }}</p>
            </div>
            @if($order->order_type === 'food' && $order->foodDetail)
            <div class="col-span-2 sm:col-span-1">
                <p class="text-[13px] text-muted mb-[4px]">Batch PO</p>
                <p class="text-ink font-medium text-[15px]">{{ $order->foodDetail->batch->title ?? '-' }}</p>
            </div>
            @elseif($order->order_type === 'ticket' && $order->ticketDetail)
            <div class="col-span-2 sm:col-span-1">
                <p class="text-[13px] text-muted mb-[4px]">Event</p>
                <p class="text-ink font-medium text-[15px]">{{ $order->ticketDetail->event->name ?? '-' }}</p>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <p class="text-[13px] text-muted mb-[4px]">Kode Booking</p>
                <p class="text-ink font-bold text-[15px] tracking-widest">{{ $order->ticketDetail->booking_code }}</p>
            </div>
            @elseif($order->order_type === 'jastip' && $order->jastipDetail)
            <div class="col-span-2 sm:col-span-1">
                <p class="text-[13px] text-muted mb-[4px]">Trip Jastip</p>
                <p class="text-ink font-medium text-[15px]">{{ $order->jastipDetail->trip->destination ?? '-' }}</p>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <p class="text-[13px] text-muted mb-[4px]">Kode Booking</p>
                <p class="text-ink font-bold text-[15px] tracking-widest">{{ $order->jastipDetail->booking_code }}</p>
            </div>
            @endif
            <div>
                <p class="text-[13px] text-muted mb-[4px]">Pengiriman</p>
                <p class="font-medium text-ink text-[15px]">{{ $order->shipping_method === 'pickup' ? 'Ambil Sendiri' : 'Delivery' }}</p>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="border-t border-hairline pt-[24px] mb-[24px]">
            <h3 class="font-bold text-ink text-[16px] mb-[16px]">Detail Pesanan</h3>
            @foreach($order->items as $item)
            <div class="flex justify-between text-[15px] py-[8px]">
                <span class="text-muted">{{ $item->product_name }} <span class="text-ink font-medium">&times; {{ $item->quantity }}</span></span>
                <span class="font-medium text-ink">{{ $item->formatted_subtotal }}</span>
            </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div class="border-t border-hairline pt-[24px] space-y-[12px]">
            <div class="flex justify-between text-[15px]">
                <span class="text-muted">Subtotal</span>
                <span class="font-medium text-ink">{{ $order->formatted_subtotal }}</span>
            </div>
            @if((float)$order->discount_amount > 0)
            <div class="flex justify-between text-[15px]">
                <span class="text-green-600">Diskon ({{ $order->promo_code_used }})</span>
                <span class="font-medium text-green-600">-{{ $order->formatted_discount }}</span>
            </div>
            @endif
            @if($order->shipping_cost !== null)
            <div class="flex justify-between text-[15px]">
                <span class="text-muted">Ongkir</span>
                <span class="font-medium text-ink">{{ $order->formatted_shipping_cost }}</span>
            </div>
            @endif
            @if($order->grand_total !== null)
            <div class="flex justify-between items-center text-[18px] border-t border-ink pt-[24px] mt-[12px]">
                <span class="font-bold text-ink">Total</span>
                <span class="font-bold text-ink">{{ $order->formatted_grand_total }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Status & Payment --}}
    <div class="bg-surface-soft border border-hairline rounded-[14px] p-[32px] mb-[32px] text-center">
        <div class="mb-[24px]">
            <span class="px-[16px] py-[8px] rounded-full text-[13px] font-bold uppercase tracking-wide {{ $order->status_color }}">{{ $order->status_label }}</span>
        </div>

        @if($whatsappLink)
        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
           class="w-full flex items-center justify-center gap-[12px] bg-[#25D366] hover:bg-[#128C7E] text-white px-[24px] py-[14px] rounded-[8px] font-medium text-[16px] transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            Bayar via WhatsApp
        </a>
        <p class="text-[13px] text-muted mt-[16px]">Silakan hubungi admin melalui WhatsApp untuk konfirmasi pembayaran.</p>
        @elseif($order->status === 'awaiting_shipping_cost')
        <div class="flex items-start gap-[8px]">
            <svg class="w-5 h-5 text-ink shrink-0 mt-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="text-[15px] font-medium text-ink mb-[2px]">Admin sedang menghitung ongkos kirim Anda.</p>
                <p class="text-[14px] text-muted">Silakan cek kembali di halaman Lacak Order untuk link pembayaran.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-[16px]">
        <a href="{{ route('track.show') }}" class="btn-primary flex-1 text-center">
            Lacak Order
        </a>
        <a href="{{ route('portal') }}" class="btn-secondary flex-1 text-center">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
