@extends('layouts.storefront')

@section('title', 'Trip ke ' . $trip->destination . ' | Mamaya Jastip')

@section('content')
<div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-[8px] text-[14px] text-muted mb-[24px]">
        <a href="{{ route('jastip.index') }}" class="hover:text-ink transition-colors">Jastip</a>
        <span>&middot;</span>
        <span class="text-ink">{{ $trip->destination }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-[48px] lg:gap-[64px]">
        
        {{-- Left Column (Trip Info) --}}
        <div class="w-full lg:w-[60%] xl:w-[65%]">
            <h1 class="text-[28px] md:text-[36px] font-bold text-ink leading-[1.2] mb-[24px]">Trip ke {{ $trip->destination }}</h1>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-[24px] mb-[32px] py-[24px] border-y border-hairline">
                <div>
                    <h3 class="text-[14px] font-bold text-ink uppercase tracking-wide mb-[8px]">Berangkat</h3>
                    <p class="text-[16px] text-muted">{{ $trip->departure_date->format('d M Y') }}</p>
                </div>
                <div>
                    <h3 class="text-[14px] font-bold text-ink uppercase tracking-wide mb-[8px]">Kembali</h3>
                    <p class="text-[16px] text-muted">{{ $trip->return_date->format('d M Y') }}</p>
                </div>
                <div>
                    <h3 class="text-[14px] font-bold text-ink uppercase tracking-wide mb-[8px]">Batas PO</h3>
                    <p class="text-[16px] font-medium text-ink">{{ $trip->po_close_date->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <div class="mb-[48px]">
                <h2 class="text-[20px] font-semibold text-ink mb-[16px]">Tentang Trip Ini</h2>
                <div class="text-[16px] text-body leading-relaxed whitespace-pre-wrap">{{ $trip->description }}</div>
            </div>
        </div>

        {{-- Right Column (Reservation Card) --}}
        <div class="w-full lg:w-[40%] xl:w-[35%]">
            <div class="sticky top-[100px] bg-canvas border border-hairline rounded-[14px] p-[24px] card-shadow-hover">
                <div class="mb-[24px] pb-[24px] border-b border-hairline">
                    <span class="text-[22px] font-bold text-ink block mb-[4px]">Open PO Jastip</span>
                    <span class="text-[14px] text-muted">Sisa Kuota: {{ $trip->baggage_quota_kg ?? '~' }} kg</span>
                </div>

                <div class="space-y-[16px] mb-[24px]">
                    <div class="flex items-start gap-[12px]">
                        <svg class="w-5 h-5 text-ink shrink-0 mt-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <div>
                            <p class="text-[14px] font-bold text-ink">Titip Barang Apapun</p>
                            <p class="text-[13px] text-muted">Kirimkan link barang yang Anda inginkan kepada kami.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-[12px]">
                        <svg class="w-5 h-5 text-ink shrink-0 mt-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-[14px] font-bold text-ink">Transparan & Terpercaya</p>
                            <p class="text-[13px] text-muted">Kami akan berikan estimasi harga sebelum Anda membayar.</p>
                        </div>
                    </div>
                </div>

                @if(now()->isBefore($trip->po_close_date))
                    <a href="{{ route('jastip.request', $trip->slug) }}" class="btn-primary w-full text-center shadow-sm">
                        Buat Request Jastip
                    </a>
                @else
                    <button disabled class="w-full py-[14px] px-[24px] rounded-sm font-medium text-[16px] bg-surface-soft text-muted cursor-not-allowed">
                        PO Sudah Ditutup
                    </button>
                @endif
                <p class="text-[13px] text-center text-muted mt-[16px]">Anda belum dikenakan biaya saat membuat request</p>
            </div>
        </div>

    </div>
</div>
@endsection
