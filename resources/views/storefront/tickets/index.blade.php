@extends('layouts.storefront')

@section('title', 'Mamaya Tickets - Event & Konser')

@section('content')
@section('top-gradient')
    <div class="absolute inset-x-0 top-0 h-[500px] bg-gradient-to-b from-blue-200/80 via-indigo-100/40 to-transparent pointer-events-none -z-10"></div>
@endsection

<div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    <div class="text-center mb-[48px]">
        <h1 class="text-[32px] md:text-[40px] font-bold text-ink tracking-tight mb-[8px] flex items-center justify-center gap-3">Mamaya Tickets <svg class="w-8 h-8 md:w-10 md:h-10 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg></h1>
        <p class="text-[16px] md:text-[18px] text-muted">Amankan tiket konser dan event favorit Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-[24px]">
        @forelse($events as $event)
        <div class="group block relative">
            <a href="{{ route('tickets.show', $event->slug) }}" class="block">
                {{-- Card Photo (Vertical 4:5 Aspect Ratio for Experience Cards) --}}
                <div class="aspect-[4/5] bg-surface-soft rounded-[14px] overflow-hidden mb-[16px] relative">
                    @if($event->banner_image)
                        <img src="{{ asset('storage/' . $event->banner_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $event->name }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-surface-strong">
                            <svg class="w-12 h-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    
                    {{-- Status Badge --}}
                    @if($event->isWarActive())
                        <div class="absolute top-[12px] left-[12px] bg-primary text-white text-[12px] font-bold px-[12px] py-[4px] rounded-full uppercase tracking-wide animate-pulse">
                            WAR IS ON!
                        </div>
                    @elseif(now()->isBefore($event->war_start_time))
                        <div class="absolute top-[12px] left-[12px] bg-surface-strong/80 backdrop-blur-sm text-ink text-[12px] font-bold px-[12px] py-[4px] rounded-full uppercase tracking-wide border border-hairline/50">
                            Coming Soon
                        </div>
                    @else
                        <div class="absolute top-[12px] left-[12px] bg-ink/80 backdrop-blur-sm text-white text-[12px] font-bold px-[12px] py-[4px] rounded-full uppercase tracking-wide">
                            Closed
                        </div>
                    @endif
                </div>
            </a>

            {{-- Card Info --}}
            <div>
                <a href="{{ route('tickets.show', $event->slug) }}">
                    <h2 class="text-[16px] font-medium text-ink leading-[1.25] mb-[4px] truncate group-hover:underline">{{ $event->name }}</h2>
                    <p class="text-[14px] text-muted truncate mb-[2px]">{{ $event->venue }}</p>
                    <p class="text-[14px] font-medium text-ink">War: {{ $event->war_start_time->format('d M, H:i') }}</p>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full border border-hairline rounded-[14px] p-[64px] text-center bg-canvas">
            <div class="mb-[16px] flex justify-center"><svg class="w-12 h-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg></div>
            <h3 class="text-[20px] font-bold text-ink mb-[8px]">Belum Ada Event Tiket</h3>
            <p class="text-[16px] text-muted">Nantikan konser-konser seru berikutnya.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
