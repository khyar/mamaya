@extends('layouts.storefront')

@section('title', 'Lacak Pesanan - Mamaya')

@section('content')
<div class="max-w-[800px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    <div class="text-center mb-[48px]">
        <h1 class="text-[32px] font-bold text-ink mb-[8px] tracking-tight">Lacak Pesanan</h1>
        <p class="text-[16px] text-muted">Masukkan No. Order dan No. HP untuk melihat status pesanan</p>
    </div>

    {{-- Search Form --}}
    <div class="bg-canvas border border-hairline rounded-[14px] p-[32px] mb-[48px] card-shadow-hover">
        <form action="{{ route('track.search') }}" method="POST" class="space-y-[24px]">
            @csrf
            <div>
                <label for="track-order-number" class="block text-[14px] font-medium text-ink mb-[8px]">No. Order</label>
                <input type="text" id="track-order-number" name="order_number" value="{{ old('order_number') }}" required maxlength="50"
                       class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas uppercase"
                       placeholder="DM-20260602-XXXX">
            </div>
            <div>
                <label for="track-phone" class="block text-[14px] font-medium text-ink mb-[8px]">No. HP</label>
                <input type="tel" id="track-phone" name="phone" value="{{ old('phone') }}" required maxlength="20"
                       class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas"
                       placeholder="08xxxxxxxxxx">
            </div>
            <button type="submit" class="btn-primary w-full shadow-sm">
                Cari Pesanan
            </button>
        </form>
    </div>

    {{-- Results --}}
    @if(isset($order))
    <div class="fade-in-up">
        
        {{-- Status Timeline --}}
        <div class="bg-canvas border border-hairline rounded-[14px] p-[32px] mb-[32px]">
            <div class="flex items-center justify-between mb-[32px]">
                <h2 class="text-[20px] font-bold text-ink">Status Pesanan</h2>
                <span class="px-[12px] py-[4px] rounded-full text-[12px] font-bold uppercase tracking-wider {{ str_replace(['bg-amber-100', 'text-amber-800'], ['bg-surface-soft', 'text-ink'], $order->status_color) }} border border-hairline">{{ $order->status_label }}</span>
            </div>

            @php
            $statusSteps = [
                ['key' => 'pending', 'label' => 'Diterima', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'],
                ['key' => 'awaiting_payment', 'label' => 'Menunggu Pembayaran', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>'],
                ['key' => 'processing', 'label' => 'Diproses', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>'],
                ['key' => 'ready', 'label' => 'Siap', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'],
                ['key' => 'completed', 'label' => 'Selesai', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'],
            ];
            $statusOrder = ['pending', 'awaiting_shipping_cost', 'awaiting_payment', 'processing', 'ready', 'completed'];
            $currentIndex = array_search($order->status, $statusOrder);
            if ($currentIndex === false) $currentIndex = -1;
            @endphp

            <div class="flex items-center justify-between relative px-[8px] sm:px-[24px]">
                <div class="absolute top-1/2 -translate-y-[20px] left-[24px] right-[24px] h-[2px] bg-hairline"></div>
                @foreach($statusSteps as $index => $step)
                @php
                    $stepIndex = array_search($step['key'], $statusOrder);
                    $isComplete = $currentIndex > $stepIndex;
                    $isCurrent = ($order->status === $step['key']) || ($order->status === 'awaiting_shipping_cost' && $step['key'] === 'awaiting_payment');
                @endphp
                <div class="relative flex flex-col items-center z-10 w-[60px] sm:w-[80px]">
                    <div class="w-[40px] h-[40px] rounded-full flex items-center justify-center text-[20px] transition-colors
                                {{ $isCurrent ? 'bg-primary text-white shadow-md' : ($isComplete ? 'bg-ink text-white' : 'bg-canvas border-2 border-hairline text-muted') }}">
                        @if($isComplete)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <span class="{{ $isCurrent ? 'opacity-100' : 'opacity-50' }}">{!! $step['icon'] !!}</span>
                        @endif
                    </div>
                    <p class="text-[11px] sm:text-[12px] mt-[12px] text-center font-bold {{ $isCurrent ? 'text-primary' : ($isComplete ? 'text-ink' : 'text-muted') }} leading-[1.2]">{{ $step['label'] }}</p>
                </div>
                @endforeach
            </div>

            @if($order->status === 'awaiting_shipping_cost')
            <div class="mt-[32px] bg-surface-soft border border-hairline rounded-[8px] p-[16px] text-center">
                <div class="flex items-start gap-[8px] justify-center">
                    <svg class="w-5 h-5 text-ink shrink-0 mt-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-left">
                        <p class="text-ink font-medium text-[14px] mb-[2px]">Admin sedang menghitung ongkos kirim Anda</p>
                        <p class="text-muted text-[13px]">Silakan cek kembali nanti untuk melihat total & link pembayaran</p>
                    </div>
                </div>
            </div>
            @endif

            @if($order->status === 'cancelled')
            <div class="mt-[32px] bg-red-50 border border-red-200 rounded-[8px] p-[16px] text-center">
                <p class="text-red-700 font-bold text-[14px] flex justify-center items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Pesanan ini telah dibatalkan</p>
            </div>
            @endif
        </div>

        {{-- WhatsApp Payment Button --}}
        @if(isset($whatsappLink) && $whatsappLink)
        <div class="bg-canvas border border-hairline rounded-[14px] p-[32px] mb-[32px] text-center">
            <h3 class="font-bold text-ink text-[18px] mb-[16px]">Pembayaran via WhatsApp</h3>
            <p class="text-[14px] text-muted mb-[24px]">Silakan lanjutkan pembayaran Anda.</p>
            
            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
               class="w-full flex items-center justify-center gap-[12px] bg-[#25D366] hover:bg-[#128C7E] text-white px-[24px] py-[14px] rounded-[8px] font-medium text-[16px] transition-colors shadow-sm mb-[16px]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                Kirim Bukti Pembayaran
            </a>
            <p class="text-[14px] text-ink font-medium">Total: {{ $order->formatted_grand_total }}</p>
        </div>
        @endif

        {{-- Order Details --}}
        <div class="bg-canvas border border-hairline rounded-[14px] p-[32px]">
            <h3 class="font-bold text-ink text-[18px] mb-[24px]">Rincian Pesanan</h3>
            
            <div class="grid grid-cols-2 gap-[16px] mb-[24px] pb-[24px] border-b border-hairline">
                <div>
                    <p class="text-muted text-[13px] mb-[4px]">No. Order</p>
                    <p class="font-medium text-ink text-[14px]">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-muted text-[13px] mb-[4px]">Tanggal Order</p>
                    <p class="font-medium text-ink text-[14px]">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                
                @if($order->order_type === 'food' && $order->foodDetail)
                <div>
                    <p class="text-muted text-[13px] mb-[4px]">Batch PO</p>
                    <p class="font-medium text-ink text-[14px]">{{ $order->foodDetail->batch->title ?? '-' }}</p>
                </div>
                @elseif($order->order_type === 'ticket' && $order->ticketDetail)
                <div>
                    <p class="text-muted text-[13px] mb-[4px]">Event</p>
                    <p class="font-medium text-ink text-[14px]">{{ $order->ticketDetail->event->name ?? '-' }}</p>
                </div>
                @elseif($order->order_type === 'jastip' && $order->jastipDetail)
                <div>
                    <p class="text-muted text-[13px] mb-[4px]">Trip Jastip</p>
                    <p class="font-medium text-ink text-[14px]">{{ $order->jastipDetail->trip->destination ?? '-' }}</p>
                </div>
                @endif
                
                <div>
                    <p class="text-muted text-[13px] mb-[4px]">Pengiriman</p>
                    <p class="font-medium text-ink text-[14px]">{{ $order->shipping_method === 'pickup' ? 'Ambil Sendiri' : 'Delivery' }}</p>
                </div>
            </div>

            <div class="space-y-[12px] mb-[24px]">
                @foreach($order->items as $item)
                <div class="flex justify-between text-[15px]">
                    <span class="text-muted">{{ $item->product_name }} <span class="text-ink font-medium">&times; {{ $item->quantity }}</span></span>
                    <span class="font-medium text-ink">{{ $item->formatted_subtotal }}</span>
                </div>
                @endforeach
            </div>

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
                <div class="flex justify-between border-t border-ink pt-[16px] mt-[16px] items-center">
                    <span class="font-bold text-ink text-[16px]">Total</span>
                    <span class="font-bold text-ink text-[18px]">{{ $order->formatted_grand_total }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
