@extends('layouts.storefront')

@section('title', 'Mamaya Jastip - Trip Luar Negeri')

@section('content')
@section('top-gradient')
    <div class="absolute inset-x-0 top-0 h-[500px] bg-gradient-to-b from-pink-200/80 via-rose-100/40 to-transparent pointer-events-none -z-10"></div>
@endsection

<div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    <div class="text-center mb-[48px]">
        <h1 class="text-[32px] md:text-[40px] font-bold text-ink tracking-tight mb-[8px] flex justify-center items-center gap-3">Mamaya Jastip <svg class="w-8 h-8 md:w-10 md:h-10 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></h1>
        <p class="text-[16px] md:text-[18px] text-muted">Titip beli barang dari luar negeri, terpercaya dan transparan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[24px]">
        @forelse($trips as $trip)
        <a href="{{ route('jastip.show', $trip->slug) }}" class="group block bg-canvas border border-hairline rounded-[14px] overflow-hidden card-shadow-hover flex flex-col">
            <div class="p-[24px] flex-1">
                <div class="flex justify-between items-start mb-[16px]">
                    <span class="inline-flex items-center gap-[6px] px-[12px] py-[4px] rounded-full bg-green-50 text-green-700 text-[11px] font-bold uppercase tracking-wide">
                        <span class="w-[6px] h-[6px] rounded-full bg-green-500 animate-pulse"></span>
                        Open PO
                    </span>
                    <span class="text-[13px] text-muted font-medium">Kuota: {{ $trip->baggage_quota_kg ?? '~' }} kg</span>
                </div>
                
                <h2 class="text-[24px] font-bold text-ink leading-[1.2] mb-[8px] group-hover:underline">{{ $trip->destination }}</h2>
                
                <div class="space-y-[4px] text-[14px] text-muted mb-[16px]">
                    <p>Berangkat: <span class="font-medium text-ink">{{ $trip->departure_date->format('d M Y') }}</span></p>
                    <p>Kembali: <span class="font-medium text-ink">{{ $trip->return_date->format('d M Y') }}</span></p>
                    <p>Tutup PO: <span class="font-medium text-ink">{{ $trip->po_close_date->format('d M Y') }}</span></p>
                </div>
                
                <p class="text-[15px] text-body line-clamp-3">{{ $trip->description }}</p>
            </div>
            
            <div class="p-[24px] pt-0">
                <span class="btn-secondary w-full pointer-events-none group-hover:border-ink transition-colors">
                    Lihat Detail Trip
                </span>
            </div>
        </a>
        @empty
        <div class="col-span-full border border-hairline rounded-[14px] p-[64px] text-center bg-canvas">
            <div class="mb-[16px] flex justify-center"><svg class="w-12 h-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <h3 class="text-[20px] font-bold text-ink mb-[8px]">Belum Ada Trip Aktif</h3>
            <p class="text-[16px] text-muted">Nantikan perjalanan jastip kami berikutnya.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
