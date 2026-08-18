@extends('layouts.admin')
@section('page-title', 'Detail Pesanan')

@section('content')
<a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-sm text-warm-500 hover:text-brand-600 mb-6">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Kembali ke Pesanan
</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Order Info --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Header --}}
        <div class="bg-white rounded-2xl border border-warm-100 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-brand-600">{{ $order->order_number }}</h2>
                    <p class="text-sm text-warm-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <span class="inline-flex px-3 py-1.5 rounded-full text-sm font-semibold {{ $order->status_color }} self-start">{{ $order->status_label }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-warm-500 text-xs mb-1">Customer</p>
                    <p class="font-medium text-warm-900">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-warm-500 text-xs mb-1">No. HP</p>
                    <p class="font-medium text-warm-900">{{ $order->customer_phone }}</p>
                </div>
                @if($order->order_type === 'food' && $order->foodDetail)
                <div>
                    <p class="text-warm-500 text-xs mb-1">Batch PO</p>
                    <p class="font-medium text-warm-900">{{ $order->foodDetail->batch->title ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-warm-500 text-xs mb-1">Pengiriman</p>
                    <p class="font-medium text-warm-900">{{ $order->foodDetail->shipping_method === 'pickup' ? '🏪 Ambil Sendiri' : '🚚 Delivery' }}</p>
                </div>
                @if($order->foodDetail->customer_address)
                <div class="col-span-2">
                    <p class="text-warm-500 text-xs mb-1">Alamat</p>
                    <p class="font-medium text-warm-900">{{ $order->foodDetail->customer_address }}</p>
                </div>
                @endif
                @elseif($order->order_type === 'ticket' && $order->ticketDetail)
                <div>
                    <p class="text-warm-500 text-xs mb-1">Event</p>
                    <p class="font-medium text-warm-900">{{ $order->ticketDetail->event->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-warm-500 text-xs mb-1">No. KTP / NIK</p>
                    <p class="font-medium text-warm-900">{{ $order->ticketDetail->ktp_number }}</p>
                </div>
                <div>
                    <p class="text-warm-500 text-xs mb-1">Email</p>
                    <p class="font-medium text-warm-900">{{ $order->ticketDetail->email_address }}</p>
                </div>
                <div>
                    <p class="text-warm-500 text-xs mb-1">Kode Booking</p>
                    <p class="font-medium text-brand-600 bg-brand-50 px-2 py-0.5 rounded text-sm inline-block">{{ $order->ticketDetail->booking_code }}</p>
                </div>
                @elseif($order->order_type === 'jastip' && $order->jastipDetail)
                <div>
                    <p class="text-warm-500 text-xs mb-1">Destinasi Jastip</p>
                    <p class="font-medium text-warm-900">{{ $order->jastipDetail->trip->destination ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-warm-500 text-xs mb-1">Kode Booking</p>
                    <p class="font-medium text-brand-600 bg-brand-50 px-2 py-0.5 rounded text-sm inline-block">{{ $order->jastipDetail->booking_code }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-warm-500 text-xs mb-1">Alamat Pengiriman</p>
                    <p class="font-medium text-warm-900">{{ $order->jastipDetail->shipping_address }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-warm-500 text-xs mb-1">Custom Request</p>
                    <p class="font-medium text-warm-900 whitespace-pre-line">{{ $order->jastipDetail->special_requests }}</p>
                </div>
                @endif
                @if($order->order_type === 'food' && $order->foodDetail && $order->foodDetail->notes)
                <div class="col-span-2">
                    <p class="text-warm-500 text-xs mb-1">Catatan</p>
                    <p class="font-medium text-warm-900">{{ $order->foodDetail->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white rounded-2xl border border-warm-100 shadow-sm p-6">
            <h3 class="font-semibold text-warm-900 mb-4">Item Pesanan</h3>
            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-warm-50' : '' }}">
                    <div>
                        <p class="text-sm font-medium text-warm-900">{{ $item->product_name }}</p>
                        <p class="text-xs text-warm-400">{{ $item->formatted_price }} × {{ $item->quantity }}</p>
                    </div>
                    <p class="text-sm font-semibold text-warm-900">{{ $item->formatted_subtotal }}</p>
                </div>
                @endforeach
            </div>

            <div class="border-t border-warm-200 mt-4 pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-warm-600">Subtotal</span>
                    <span class="font-medium">{{ $order->formatted_subtotal }}</span>
                </div>
                @if((float)$order->discount_amount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-accent-600">Diskon ({{ $order->promo_code_used }})</span>
                    <span class="font-medium text-accent-600">-{{ $order->formatted_discount }}</span>
                </div>
                @endif
                @if($order->order_type === 'food' && $order->foodDetail && $order->foodDetail->shipping_cost !== null)
                <div class="flex justify-between text-sm">
                    <span class="text-warm-600">Ongkir</span>
                    <span class="font-medium">{{ $order->formatted_shipping_cost }}</span>
                </div>
                @endif
                @if($order->grand_total !== null)
                <div class="flex justify-between text-lg border-t border-warm-200 pt-3 mt-3">
                    <span class="font-semibold text-warm-900">Grand Total</span>
                    <span class="font-bold text-brand-600">{{ $order->formatted_grand_total }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions Sidebar --}}
    <div class="space-y-6">
        {{-- Shipping Cost Input (for delivery orders awaiting shipping cost) --}}
        @if($order->order_type === 'food' && $order->foodDetail && $order->foodDetail->shipping_method === 'delivery' && in_array($order->status, ['pending', 'awaiting_shipping_cost']))
        <div class="bg-orange-50 rounded-2xl border border-orange-200 p-6">
            <h3 class="font-semibold text-orange-900 mb-3 flex items-center gap-2">
                <span>🚚</span> Isi Ongkir
            </h3>
            <form action="{{ route('admin.orders.shipping-cost', $order) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label for="shipping-cost-input" class="block text-xs font-medium text-orange-700 mb-1">Biaya Ongkir (Rp)</label>
                    <input type="number" id="shipping-cost-input" name="shipping_cost" min="0" max="999999" step="500" required
                           class="w-full border border-orange-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                           placeholder="15000">
                    @error('shipping_cost') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white py-2.5 rounded-xl font-semibold text-sm hover:bg-orange-600 transition-colors">
                    Simpan & Kirim ke Customer
                </button>
                <p class="text-xs text-orange-600">Grand total akan dihitung otomatis dan customer bisa melihat link pembayaran di halaman Lacak Order.</p>
            </form>
        </div>
        @endif

        {{-- Jastip Quotation Input (for custom request jastip orders) --}}
        @if($order->order_type === 'jastip' && $order->status === 'pending')
        <div class="bg-indigo-50 rounded-2xl border border-indigo-200 p-6">
            <h3 class="font-semibold text-indigo-900 mb-3 flex items-center gap-2">
                <span>💰</span> Berikan Penawaran Harga
            </h3>
            <form action="{{ route('admin.orders.jastip-quotation', $order) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label for="quotation-price-input" class="block text-xs font-medium text-indigo-700 mb-1">Harga Final (Rp)</label>
                    <input type="number" id="quotation-price-input" name="quotation_price" min="0" step="1000" required
                           class="w-full border border-indigo-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Contoh: 450000">
                    @error('quotation_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-xl font-semibold text-sm hover:bg-indigo-700 transition-colors">
                    Simpan & Update Status
                </button>
                <p class="text-xs text-indigo-600">Total belanja jastip + fee jastip. Akan langsung mengubah status pesanan menjadi Menunggu Pembayaran.</p>
            </form>
        </div>
        @endif

        {{-- WhatsApp Link --}}
        @if(isset($whatsappLink) && $whatsappLink)
        <div class="bg-white rounded-2xl border border-warm-100 shadow-sm p-6">
            <h3 class="font-semibold text-warm-900 mb-3">Link Pembayaran</h3>
            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
               class="w-full bg-green-500 text-white py-2.5 rounded-xl font-semibold text-sm hover:bg-green-600 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                Buka WhatsApp
            </a>
        </div>
        @endif

        {{-- Update Status --}}
        <div class="bg-white rounded-2xl border border-warm-100 shadow-sm p-6">
            <h3 class="font-semibold text-warm-900 mb-3">Ubah Status</h3>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <select name="status" class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-warm-900 text-white py-2.5 rounded-xl font-semibold text-sm hover:bg-warm-800 transition-colors">
                    Perbarui Status
                </button>
            </form>
        </div>

        {{-- Payment Info --}}
        @if($order->paid_at)
        <div class="bg-green-50 rounded-2xl border border-green-200 p-6">
            <h3 class="font-semibold text-green-900 mb-2 flex items-center gap-2">
                <span>✅</span> Sudah Dibayar
            </h3>
            <p class="text-sm text-green-700">{{ $order->paid_at->format('d M Y, H:i') }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
