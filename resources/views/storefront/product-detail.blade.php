@extends('layouts.storefront')

@section('title', $product->name . ' - Mamaya Food')

@section('content')
<div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-[8px] text-[14px] text-muted mb-[24px]">
        <a href="{{ route('food.home') }}" class="hover:text-ink transition-colors">Food</a>
        <span>&middot;</span>
        <a href="{{ route('food.products') }}" class="hover:text-ink transition-colors">Menu</a>
        <span>&middot;</span>
        <span class="text-ink">{{ $product->name }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-[48px] lg:gap-[64px]">
        
        {{-- Left Column (Product Info & Photos) --}}
        <div class="w-full lg:w-[60%] xl:w-[65%]">
            {{-- Image Gallery --}}
            <div x-data="{ activeImage: '{{ $product->primaryImageUrl }}' }" class="mb-[48px]">
                <div class="aspect-[4/3] rounded-[14px] overflow-hidden bg-surface-soft mb-[16px]">
                    <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
                @if($product->images->count() > 0)
                <div class="flex gap-[12px] overflow-x-auto pb-[8px] scrollbar-hide">
                    @if($product->image)
                    <button @click="activeImage = '{{ asset('storage/' . $product->image) }}'" class="w-[80px] h-[80px] rounded-[8px] overflow-hidden border border-hairline hover:border-ink transition-colors flex-shrink-0 focus:outline-none focus:border-ink">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover opacity-80 hover:opacity-100 transition-opacity">
                    </button>
                    @endif
                    @foreach($product->images as $image)
                    <button @click="activeImage = '{{ $image->url }}'" class="w-[80px] h-[80px] rounded-[8px] overflow-hidden border border-hairline hover:border-ink transition-colors flex-shrink-0 focus:outline-none focus:border-ink">
                        <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover opacity-80 hover:opacity-100 transition-opacity">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Title & Description --}}
            <div>
                <div class="flex items-start justify-between mb-[24px]">
                    <h1 class="text-[28px] md:text-[32px] font-bold text-ink leading-[1.2]">{{ $product->name }}</h1>
                </div>

                @if($product->description)
                <div class="py-[32px] border-t border-hairline">
                    <h2 class="text-[20px] font-semibold text-ink mb-[16px]">Tentang Menu Ini</h2>
                    <div class="text-[16px] text-body leading-relaxed">
                        <p>{{ $product->description }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Right Column (Reservation Card) --}}
        <div class="w-full lg:w-[40%] xl:w-[35%]">
            <div class="sticky top-[100px] bg-canvas border border-hairline rounded-[14px] p-[24px] card-shadow-hover">
                <div class="mb-[24px]">
                    <span class="text-[22px] font-bold text-ink">{{ $product->formatted_price }}</span>
                </div>

                @if($activeBatches->count() > 0)
                <form action="{{ route('food.cart.add') }}" method="POST" x-data="{ qty: 1 }">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="border border-hairline rounded-[8px] overflow-hidden mb-[16px]">
                        {{-- Batch Selector --}}
                        <div class="p-[12px] border-b border-hairline relative">
                            <label class="block text-[12px] font-bold text-ink uppercase tracking-wide mb-[4px]">Batch PO</label>
                            <select name="batch_id" class="w-full bg-transparent text-[14px] text-ink focus:outline-none cursor-pointer appearance-none">
                                @foreach($activeBatches as $batch)
                                <option value="{{ $batch->id }}">{{ $batch->title }} (Tutup: {{ $batch->close_date->format('d M') }})</option>
                                @endforeach
                            </select>
                            <div class="absolute right-[12px] top-1/2 translate-y-[-10%] pointer-events-none text-ink">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        {{-- Quantity --}}
                        <div class="p-[12px] flex items-center justify-between">
                            <label class="block text-[12px] font-bold text-ink uppercase tracking-wide">Jumlah</label>
                            <div class="flex items-center gap-[12px]">
                                <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-[28px] h-[28px] rounded-full border border-hairline flex items-center justify-center text-muted hover:border-ink hover:text-ink transition-colors">&minus;</button>
                                <input type="number" name="quantity" x-model="qty" min="1" max="100" class="w-[32px] text-center text-[16px] text-ink focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none bg-transparent">
                                <button type="button" @click="qty = Math.min(100, qty + 1)" class="w-[28px] h-[28px] rounded-full border border-hairline flex items-center justify-center text-muted hover:border-ink hover:text-ink transition-colors">&plus;</button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full shadow-sm">
                        Pesan Sekarang
                    </button>
                    <p class="text-[13px] text-center text-muted mt-[16px]">Anda belum dikenakan biaya</p>
                </form>
                @else
                <div class="bg-surface-soft p-[16px] rounded-[8px] text-center">
                    <p class="text-[14px] text-ink font-medium">Belum ada batch PO aktif</p>
                    <p class="text-[13px] text-muted mt-1">Silakan kembali lagi nanti.</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
