@extends('layouts.storefront')
@section('title', 'Checkout Tiket: ' . $event->name)

@section('content')
<div class="max-w-[800px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]">
    <div class="mb-[48px]">
        <h1 class="text-[32px] md:text-[40px] font-bold text-ink tracking-tight mb-[8px]">Form Pemesanan Tiket</h1>
        <p class="text-[16px] text-muted">{{ $event->name }}</p>
    </div>

    <div class="bg-canvas border border-hairline rounded-[14px] overflow-hidden card-shadow-hover">
        <form action="{{ route('tickets.checkout.process', $event->slug) }}" method="POST" class="p-[32px] space-y-[32px]">
            @csrf

            <div class="bg-surface-soft border border-hairline rounded-[8px] p-[16px]">
                <div class="flex gap-[12px] items-start">
                    <svg class="w-5 h-5 text-ink shrink-0 mt-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-[14px] font-bold text-ink mb-[2px]">PENTING: Siapkan KTP Anda.</p>
                        <p class="text-[13px] text-muted">Nama harus persis sesuai KTP untuk penukaran tiket fisik di venue.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-[24px]">
                <div>
                    <label class="block text-[14px] font-medium text-ink mb-[8px]">Pilih Kategori Tiket</label>
                    <div class="relative">
                        <select name="category_id" required class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas appearance-none cursor-pointer">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($event->categories as $category)
                                <option value="{{ $category->id }}" @disabled($category->available_quota <= 0)>
                                    {{ $category->name }} - Rp {{ number_format($category->price, 0, ',', '.') }} {{ $category->available_quota <= 0 ? '(Habis)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute right-[16px] top-1/2 -translate-y-1/2 pointer-events-none text-ink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    @error('category_id') <span class="text-primary-error-text text-[13px] mt-[4px] block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[14px] font-medium text-ink mb-[8px]">Jumlah Tiket (Max 4)</label>
                    <div class="relative">
                        <select name="quantity" required class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas appearance-none cursor-pointer">
                            <option value="1">1 Tiket</option>
                            <option value="2">2 Tiket</option>
                            <option value="3">3 Tiket</option>
                            <option value="4">4 Tiket</option>
                        </select>
                        <div class="absolute right-[16px] top-1/2 -translate-y-1/2 pointer-events-none text-ink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    @error('quantity') <span class="text-primary-error-text text-[13px] mt-[4px] block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-[14px] font-medium text-ink mb-[8px]">Nama Sesuai KTP</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas" placeholder="JOHN DOE">
                @error('customer_name') <span class="text-primary-error-text text-[13px] mt-[4px] block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[14px] font-medium text-ink mb-[8px]">Nomor KTP (NIK)</label>
                <input type="text" name="ktp_number" value="{{ old('ktp_number') }}" required maxlength="16" minlength="16" class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas" placeholder="16 Digit NIK">
                @error('ktp_number') <span class="text-primary-error-text text-[13px] mt-[4px] block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-[24px]">
                <div>
                    <label class="block text-[14px] font-medium text-ink mb-[8px]">Email Aktif</label>
                    <input type="email" name="email_address" value="{{ old('email_address') }}" required class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas" placeholder="email@contoh.com">
                    @error('email_address') <span class="text-primary-error-text text-[13px] mt-[4px] block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[14px] font-medium text-ink mb-[8px]">Nomor WhatsApp</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas" placeholder="08123456789">
                    @error('customer_phone') <span class="text-primary-error-text text-[13px] mt-[4px] block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-[32px] border-t border-hairline">
                <button type="submit" class="btn-primary w-full shadow-sm">
                    Amankan Tiket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
