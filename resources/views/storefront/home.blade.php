@extends('layouts.storefront')

@section('title', 'Mamaya Food - Pre-Order Makanan Rumahan')

@section('content')
@section('top-gradient')
    <div class="absolute inset-x-0 top-0 h-[500px] bg-gradient-to-b from-orange-200/80 via-amber-100/40 to-transparent pointer-events-none -z-10"></div>
@endsection

{{-- Hero Section --}}
<section class="bg-canvas pt-[96px] pb-[64px] px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto text-center flex flex-col items-center">
    <div class="max-w-[800px]">
        <h1 class="text-[40px] md:text-[56px] font-bold text-ink leading-[1.1] mb-[16px] tracking-tight fade-in-up">
            Masakan Rumahan.<br>Rasa Istimewa.
        </h1>
        <p class="text-[18px] md:text-[20px] text-muted max-w-[600px] mx-auto mb-[32px] fade-in-up" style="animation-delay: 0.1s">
            Pre-order hidangan rumahan berkualitas dari Dapur Mamaya tanpa pengawet.
        </p>

        @if($selectedBatch)
        <div class="inline-flex items-center gap-2 bg-surface-soft border border-hairline rounded-full px-[16px] py-[8px] mb-[32px] fade-in-up" style="animation-delay: 0.2s">
            <div class="w-[8px] h-[8px] bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-[14px] font-medium text-ink">{{ $selectedBatch->title }}</span>
            <span class="text-[14px] text-muted ml-1">&middot; Tutup: {{ $selectedBatch->close_date->format('d M') }}</span>
        </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-4 justify-center fade-in-up" style="animation-delay: 0.3s">
            <a href="{{ route('food.products') }}" class="btn-primary w-full sm:w-auto">
                Lihat Menu
            </a>
            <a href="{{ route('track.show') }}" class="btn-secondary w-full sm:w-auto">
                Lacak Order
            </a>
        </div>
    </div>
</section>

{{-- Active Batches Section --}}
@if($activeBatches->count() > 0)
<section class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 pb-[64px]">
    <div class="grid grid-cols-1 md:grid-cols-{{ min($activeBatches->count(), 3) }} gap-[16px]">
        @foreach($activeBatches as $batch)
        <div class="bg-surface-card rounded-[14px] p-[24px] border border-hairline card-shadow-hover flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-[48px] h-[48px] rounded-full bg-surface-soft flex items-center justify-center">
                    <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-ink text-[16px]">{{ $batch->title }}</h3>
                    <p class="text-[14px] text-muted">Tutup: {{ $batch->close_date->format('d M Y') }}</p>
                </div>
            </div>
            @if($batch->ready_date)
            <div class="hidden sm:block text-right">
                <span class="text-[11px] font-bold bg-surface-soft text-ink uppercase tracking-wide px-3 py-1 rounded-full">
                    Siap {{ $batch->ready_date->format('d M') }}
                </span>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- Featured Products --}}
@if($products->count() > 0)
<section class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[64px]">
    <div class="mb-[32px] flex items-end justify-between">
        <div>
            <h2 class="text-[28px] font-bold text-ink tracking-tight">Menu Pilihan</h2>
            <p class="text-[16px] text-muted mt-1">Hidangan terbaik yang kami siapkan untuk Anda</p>
        </div>
        <a href="{{ route('food.products') }}" class="hidden sm:inline-flex items-center gap-1 text-[14px] font-medium text-ink hover:text-muted transition-colors">
            Lihat semua <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-[16px] md:gap-[24px]">
        @foreach($products as $product)
        <a href="{{ route('food.product.detail', $product->slug) }}" class="group block relative">
            {{-- Property Card Photo --}}
            <div class="aspect-square bg-surface-soft rounded-[14px] overflow-hidden mb-[16px]">
                <img src="{{ $product->primaryImageUrl }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     loading="lazy">
                
                {{-- Heart icon placeholder (Top Right) --}}
                <button class="absolute top-[12px] right-[12px] w-[32px] h-[32px] rounded-full bg-surface-strong/50 backdrop-blur-sm flex items-center justify-center hover:bg-surface-strong transition-colors border border-hairline/50">
                    <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
            </div>
            
            {{-- Property Card Meta --}}
            <div>
                <h3 class="font-medium text-ink text-[16px] leading-[1.25] mb-[2px] truncate group-hover:underline">{{ $product->name }}</h3>
                <p class="text-[14px] text-muted mb-[4px] truncate">{{ $product->category ?? 'Makanan Utama' }}</p>
                <p class="text-[14px] font-semibold text-ink">{{ $product->formatted_price }}</p>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-[32px] sm:hidden">
        <a href="{{ route('food.products') }}" class="btn-secondary w-full">
            Lihat semua menu
        </a>
    </div>
</section>
@endif

{{-- How It Works (Bento style) --}}
<section class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[64px] border-t border-hairline">
    <div class="mb-[48px]">
        <h2 class="text-[28px] font-bold text-ink tracking-tight">Cara Order</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-[24px]">
        @php
        $steps = [
            ['icon' => '1', 'title' => 'Pilih Batch', 'desc' => 'Pilih batch PO yang sedang aktif'],
            ['icon' => '2', 'title' => 'Pilih Menu', 'desc' => 'Tambahkan menu favorit ke keranjang'],
            ['icon' => '3', 'title' => 'Checkout', 'desc' => 'Isi data & transfer via WhatsApp'],
            ['icon' => '4', 'title' => 'Terima', 'desc' => 'Ambil atau kami antar pesananmu!'],
        ];
        @endphp
        @foreach($steps as $step)
        <div>
            <div class="text-[12px] font-bold text-ink uppercase tracking-[0.1em] mb-[12px]">Langkah {{ $step['icon'] }}</div>
            <h3 class="font-medium text-ink text-[16px] mb-[4px]">{{ $step['title'] }}</h3>
            <p class="text-[14px] text-muted">{{ $step['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>
@endsection
