@extends('layouts.storefront')

@section('title', $event->name . ' | Mamaya Tickets')

@section('content')
<div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-[8px] text-[14px] text-muted mb-[24px]">
        <a href="{{ route('tickets.index') }}" class="hover:text-ink transition-colors">Tickets</a>
        <span>&middot;</span>
        <span class="text-ink">{{ $event->name }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-[48px] lg:gap-[64px]">
        
        {{-- Left Column (Event Info & Photo) --}}
        <div class="w-full lg:w-[60%] xl:w-[65%]">
            
            <div class="aspect-[16/9] md:aspect-[2/1] lg:aspect-[16/9] rounded-[14px] overflow-hidden bg-surface-soft mb-[32px] border border-hairline relative">
                @if($event->banner_image)
                    <img src="{{ asset('storage/' . $event->banner_image) }}" class="w-full h-full object-cover" alt="{{ $event->name }}">
                @else
                    <div class="w-full h-full flex items-center justify-center text-muted">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </div>

            <div>
                <h1 class="text-[28px] md:text-[36px] font-bold text-ink leading-[1.2] mb-[24px]">{{ $event->name }}</h1>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-[24px] mb-[32px] py-[24px] border-y border-hairline">
                    <div>
                        <h3 class="text-[14px] font-bold text-ink uppercase tracking-wide mb-[8px]">Lokasi & Waktu</h3>
                        <p class="text-[16px] text-muted mb-[4px]">{{ $event->venue }}</p>
                        <p class="text-[16px] text-muted">{{ $event->event_date->format('l, d F Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-[14px] font-bold text-ink uppercase tracking-wide mb-[8px]">Jadwal War</h3>
                        <p class="text-[16px] text-muted mb-[4px]">Mulai: <span class="font-medium text-ink">{{ $event->war_start_time->format('d M Y, H:i') }}</span></p>
                        <p class="text-[16px] text-muted">Selesai: <span class="font-medium text-ink">{{ $event->war_end_time->format('d M Y, H:i') }}</span></p>
                    </div>
                </div>

                <div class="mb-[48px]">
                    <h2 class="text-[20px] font-semibold text-ink mb-[16px]">Tentang Event Ini</h2>
                    <div class="text-[16px] text-body leading-relaxed whitespace-pre-wrap">{{ $event->description }}</div>
                </div>
            </div>
        </div>

        {{-- Right Column (Reservation Card) --}}
        <div class="w-full lg:w-[40%] xl:w-[35%]">
            <div class="sticky top-[100px] bg-canvas border border-hairline rounded-[14px] p-[24px] card-shadow-hover">
                <div class="mb-[24px]">
                    <h3 class="text-[22px] font-bold text-ink">Kategori Tiket</h3>
                </div>

                <div class="space-y-[12px] mb-[32px]">
                    @foreach($event->categories as $category)
                    <div class="border border-hairline rounded-[8px] p-[16px]">
                        <div class="flex justify-between items-start mb-[4px]">
                            <p class="font-medium text-ink text-[16px]">{{ $category->name }}</p>
                            @if($category->available_quota > 0)
                                <span class="text-[11px] font-bold bg-green-50 text-green-700 px-[8px] py-[2px] rounded-full uppercase tracking-wide">Tersedia</span>
                            @else
                                <span class="text-[11px] font-bold bg-surface-strong text-muted px-[8px] py-[2px] rounded-full uppercase tracking-wide">Habis</span>
                            @endif
                        </div>
                        <p class="text-[15px] font-medium text-muted">Rp {{ number_format($category->price, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>

                @if($event->isWarActive())
                    <a href="{{ route('tickets.checkout', $event->slug) }}" class="btn-primary w-full shadow-sm flex justify-center items-center gap-2">
                        Ikut War Sekarang!
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/></svg>
                    </a>
                @elseif(now()->isBefore($event->war_start_time))
                    <button disabled class="w-full py-[14px] px-[24px] rounded-sm font-medium text-[16px] bg-surface-soft text-muted cursor-not-allowed">
                        Belum Dibuka
                    </button>
                    <p class="text-[13px] text-center text-muted mt-[16px]">War akan dimulai pada {{ $event->war_start_time->format('d M, H:i') }}</p>
                @else
                    <button disabled class="w-full py-[14px] px-[24px] rounded-sm font-medium text-[16px] bg-surface-soft text-muted cursor-not-allowed">
                        Sudah Ditutup
                    </button>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
