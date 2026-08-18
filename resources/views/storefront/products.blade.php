@extends('layouts.storefront')

@section('title', 'Menu - Dapur Mamaya')

@section('content')
<div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    {{-- Page Header --}}
    <div class="mb-[32px]">
        <h1 class="text-[32px] md:text-[40px] font-bold text-ink mb-[8px] tracking-tight">Menu Kami</h1>
        <p class="text-[16px] text-muted">Pilih batch pre-order yang aktif dan pesan menu favoritmu</p>
    </div>

    {{-- Batch Selector --}}
    @if($activeBatches->count() > 0)
    <div class="mb-[32px] flex flex-wrap gap-[12px]" x-data>
        @foreach($activeBatches as $batch)
        <a href="{{ route('food.products', ['batch' => $batch->id]) }}"
           class="inline-flex items-center gap-[8px] px-[20px] py-[10px] rounded-full border text-[14px] font-medium transition-all duration-200
                  {{ $selectedBatch && $selectedBatch->id === $batch->id
                     ? 'border-ink bg-ink text-white shadow-sm'
                     : 'border-hairline bg-canvas text-ink hover:border-ink hover:bg-surface-soft' }}">
            <div class="w-[6px] h-[6px] rounded-full {{ $selectedBatch && $selectedBatch->id === $batch->id ? 'bg-primary' : 'bg-muted' }}"></div>
            {{ $batch->title }}
        </a>
        @endforeach
    </div>

    @if($selectedBatch)
    <div class="bg-surface-soft rounded-[14px] p-[20px] mb-[48px] flex flex-col sm:flex-row sm:items-center gap-[16px] border border-hairline">
        <div class="flex items-center gap-[8px]">
            <div class="w-[8px] h-[8px] bg-green-500 rounded-full animate-pulse"></div>
            <span class="font-bold text-ink text-[16px]">{{ $selectedBatch->title }}</span>
        </div>
        <div class="flex flex-wrap gap-[16px] text-[14px] text-ink">
            <span class="flex items-center gap-1"><span class="text-muted">Tutup:</span> <strong class="font-medium">{{ $selectedBatch->close_date->format('d M Y, H:i') }}</strong></span>
            @if($selectedBatch->ready_date)
            <span class="flex items-center gap-1"><span class="text-muted">Siap:</span> <strong class="font-medium">{{ $selectedBatch->ready_date->format('d M Y') }}</strong></span>
            @endif
            @if($selectedBatch->delivery_date)
            <span class="flex items-center gap-1"><span class="text-muted">Kirim:</span> <strong class="font-medium">{{ $selectedBatch->delivery_date->format('d M Y') }}</strong></span>
            @endif
        </div>
    </div>
    @endif
    @else
    <div class="bg-surface-soft rounded-[14px] p-[48px] text-center border border-hairline">
        <div class="mb-[16px] flex justify-center"><svg class="w-12 h-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
        <h3 class="text-[20px] font-bold text-ink mb-[8px]">Belum Ada Batch Aktif</h3>
        <p class="text-[16px] text-muted">Saat ini belum ada batch pre-order yang dibuka. Nantikan update selanjutnya!</p>
    </div>
    @endif

    {{-- Product Grid --}}
    @if($products->count() > 0)
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-[16px] md:gap-[24px]">
        @foreach($products as $product)
        <div class="group block relative" x-data="{ qty: 1 }">
            <a href="{{ route('food.product.detail', $product->slug) }}">
                <div class="aspect-square bg-surface-soft rounded-[14px] overflow-hidden mb-[16px]">
                    <img src="{{ $product->primaryImageUrl }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         loading="lazy">
                </div>
            </a>
            
            <div class="flex flex-col">
                <a href="{{ route('food.product.detail', $product->slug) }}">
                    <h3 class="font-medium text-ink text-[16px] leading-[1.25] mb-[2px] truncate group-hover:underline">{{ $product->name }}</h3>
                    <p class="text-[14px] text-muted mb-[8px] truncate">{{ $product->category ?? 'Makanan Utama' }}</p>
                    <p class="text-[14px] font-semibold text-ink mb-[16px]">{{ $product->formatted_price }}</p>
                </a>

                @if($selectedBatch)
                <form action="{{ route('food.cart.add') }}" method="POST" class="flex flex-col gap-[8px]">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="batch_id" value="{{ $selectedBatch->id }}">

                    <div class="flex items-center justify-between border border-hairline rounded-[8px] bg-canvas overflow-hidden h-[40px]">
                        <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-[40px] h-[40px] flex items-center justify-center text-ink hover:bg-surface-soft transition-colors">&minus;</button>
                        <input type="number" name="quantity" x-model="qty" min="1" max="100" class="w-[40px] text-center text-[14px] font-medium text-ink focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <button type="button" @click="qty = Math.min(100, qty + 1)" class="w-[40px] h-[40px] flex items-center justify-center text-ink hover:bg-surface-soft transition-colors">&plus;</button>
                    </div>
                    
                    <button type="submit" class="btn-primary !h-[40px] !text-[14px] !py-0 w-full flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Tambah
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @elseif($selectedBatch)
    <div class="bg-surface-soft rounded-[14px] p-[48px] text-center border border-hairline">
        <div class="mb-[16px] flex justify-center"><svg class="w-12 h-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v18M21 3v18m-9-9v9m0-9V3m-5 9h10"/></svg></div>
        <h3 class="text-[20px] font-bold text-ink mb-[8px]">Belum Ada Menu</h3>
        <p class="text-[16px] text-muted">Menu untuk batch ini belum tersedia.</p>
    </div>
    @endif
</div>
@endsection
