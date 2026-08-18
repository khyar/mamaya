@extends('layouts.storefront')
@section('title', 'Request Jastip: ' . $trip->destination)

@section('content')
<div class="max-w-[800px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    <div class="mb-[48px]">
        <h1 class="text-[32px] md:text-[40px] font-bold text-ink tracking-tight mb-[8px]">Form Request Jastip</h1>
        <p class="text-[16px] text-muted">Trip ke {{ $trip->destination }} (Tutup: {{ $trip->po_close_date->format('d M Y') }})</p>
    </div>

    <div class="bg-canvas border border-hairline rounded-[14px] overflow-hidden card-shadow-hover">
        <form action="{{ route('jastip.request.process', $trip->slug) }}" method="POST" class="p-[32px] space-y-[24px]">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-[24px]">
                <div>
                    <label class="block text-[14px] font-medium text-ink mb-[8px]">Nama Lengkap</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas" placeholder="Sesuai KTP/Identitas">
                    @error('customer_name') <span class="text-primary-error-text text-[13px] mt-[4px] block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[14px] font-medium text-ink mb-[8px]">Nomor WhatsApp</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas" placeholder="08123456789">
                    @error('customer_phone') <span class="text-primary-error-text text-[13px] mt-[4px] block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-[14px] font-medium text-ink mb-[8px]">Alamat Pengiriman</label>
                <textarea name="shipping_address" required rows="3" class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas" placeholder="Alamat lengkap penerima barang..."></textarea>
                @error('shipping_address') <span class="text-primary-error-text text-[13px] mt-[4px] block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[14px] font-medium text-ink mb-[8px]">Barang yang Ingin Dititip (Link / Deskripsi)</label>
                <textarea name="special_requests" required rows="4" class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas" placeholder="Tuliskan detail barang yang diinginkan. Contoh:&#10;1. Sepatu Nike Air Max 97 Warna Putih Ukuran 42 (Link: https://...)&#10;2. Parfum ..."></textarea>
                <p class="text-[13px] text-muted mt-[4px]">Admin kami akan mengecek ketersediaan dan memberikan estimasi total biaya termasuk fee jastip via WhatsApp.</p>
                @error('special_requests') <span class="text-primary-error-text text-[13px] mt-[4px] block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-[32px] border-t border-hairline">
                <button type="submit" class="btn-primary w-full shadow-sm">
                    Kirim Request Estimasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
