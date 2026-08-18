@extends('layouts.storefront')

@section('title', 'Keranjang - Mamaya Food')

@section('content')
<div class="max-w-[800px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    <div class="mb-[32px]">
        <h1 class="text-[32px] font-bold text-ink tracking-tight mb-[8px]">Keranjang Belanja</h1>
        @if($batch)
        <div class="inline-flex items-center gap-[8px] bg-surface-soft px-[12px] py-[6px] rounded-full border border-hairline">
            <span class="w-[8px] h-[8px] bg-green-500 rounded-full animate-pulse"></span>
            <span class="text-[14px] font-medium text-ink">Batch: {{ $batch->title }}</span>
        </div>
        @endif
    </div>

    @if(count($cartItems) > 0)
    <div class="border border-hairline rounded-[14px] overflow-hidden mb-[32px]">
        @foreach($cartItems as $index => $item)
        <div class="flex items-center gap-[16px] p-[24px] bg-canvas {{ $index > 0 ? 'border-t border-hairline' : '' }}">
            {{-- Image --}}
            <div class="w-[80px] h-[80px] rounded-[8px] overflow-hidden bg-surface-soft flex-shrink-0">
                <img src="{{ $item['product']->primaryImageUrl }}" alt="{{ $item['product']->name }}" class="w-full h-full object-cover">
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <h3 class="font-medium text-[16px] text-ink truncate mb-[4px]">{{ $item['product']->name }}</h3>
                <p class="text-[14px] font-semibold text-ink">{{ $item['product']->formatted_price }}</p>
            </div>

            {{-- Quantity Control --}}
            <form action="{{ route('food.cart.update') }}" method="POST" class="flex items-center gap-[12px]" x-data="{ qty: {{ $item['quantity'] }} }">
                @csrf
                @method('PATCH')
                <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                <div class="flex items-center border border-hairline rounded-full overflow-hidden h-[36px]">
                    <button type="submit" @click="qty = Math.max(0, qty - 1)" class="w-[36px] h-[36px] flex items-center justify-center text-muted hover:bg-surface-soft hover:text-ink transition-colors">&minus;</button>
                    <input type="number" name="quantity" x-model="qty" min="0" max="100" class="w-[36px] text-center text-[14px] font-medium text-ink focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none bg-transparent">
                    <button type="button" @click="qty = Math.min(100, qty + 1)" class="w-[36px] h-[36px] flex items-center justify-center text-muted hover:bg-surface-soft hover:text-ink transition-colors">&plus;</button>
                </div>
            </form>

            {{-- Line Total --}}
            <div class="text-right hidden sm:block min-w-[120px]">
                <p class="font-bold text-[16px] text-ink">Rp {{ number_format($item['line_total'], 0, ',', '.') }}</p>
            </div>

            {{-- Remove --}}
            <form action="{{ route('food.cart.remove', $item['product']->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-[8px] text-muted hover:text-primary-error-text transition-colors rounded-full hover:bg-surface-soft">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
        @endforeach
    </div>

    {{-- Summary --}}
    <div class="border border-hairline rounded-[14px] p-[32px] mb-[32px] bg-canvas">
        <div class="flex items-center justify-between mb-[16px]">
            <span class="text-[16px] text-muted">Subtotal</span>
            <span class="text-[24px] font-bold text-ink">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
        <p class="text-[14px] text-muted mb-[32px]">Diskon & ongkos kirim akan dihitung saat checkout.</p>

        <div class="flex flex-col sm:flex-row gap-[16px]">
            <a href="{{ route('food.checkout') }}" class="btn-primary flex-1 text-center">
                Lanjut ke Pembayaran
            </a>
            <a href="{{ route('food.products', ['batch' => $batch?->id]) }}" class="btn-secondary flex-1 text-center">
                Kembali Belanja
            </a>
        </div>
    </div>

    {{-- Clear Cart --}}
    <div class="text-center">
        <form action="{{ route('food.cart.clear') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-[14px] text-muted hover:text-primary-error-text transition-colors underline">Kosongkan keranjang</button>
        </form>
    </div>

    @else
    <div class="border border-hairline rounded-[14px] p-[64px] text-center bg-canvas">
        <div class="mb-[16px] flex justify-center"><svg class="w-12 h-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
        <h3 class="text-[20px] font-bold text-ink mb-[8px]">Keranjang Kosong</h3>
        <p class="text-[16px] text-muted mb-[32px]">Belum ada menu di keranjang Anda. Yuk mulai pre-order!</p>
        <a href="{{ route('food.products') }}" class="btn-primary inline-flex">Lihat Menu</a>
    </div>
    @endif
</div>
@endsection
