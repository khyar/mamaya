@extends('layouts.storefront')

@section('title', 'Checkout - Mamaya Food')

@section('content')
<div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px] md:py-[64px]"
     x-data="{
         shipping: 'pickup',
         promoCode: '',
         promoMessage: '',
         promoValid: false,
         promoDiscount: 0,
         promoLoading: false,
         subtotal: {{ $subtotal }},
         get shippingLabel() { return this.shipping === 'pickup' ? 'Ambil Sendiri (Gratis)' : 'Delivery (Ongkir dihitung admin)' },
         get total() {
             let t = this.subtotal - this.promoDiscount;
             return t > 0 ? t : 0;
         },
         formatRp(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n); },
         async validatePromo() {
             if (!this.promoCode.trim()) return;
             this.promoLoading = true;
             try {
                 const res = await fetch('{{ route('food.checkout.validate-promo') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                         'Accept': 'application/json',
                     },
                     body: JSON.stringify({
                         code: this.promoCode,
                         batch_id: {{ $batch->id }},
                         subtotal: this.subtotal,
                     }),
                 });
                 const data = await res.json();
                 this.promoMessage = data.message;
                 this.promoValid = data.valid;
                 this.promoDiscount = data.valid ? data.discount : 0;
             } catch (e) {
                 this.promoMessage = 'Gagal memvalidasi kode promo.';
                 this.promoValid = false;
                 this.promoDiscount = 0;
             }
             this.promoLoading = false;
         },
         clearPromo() {
             this.promoCode = '';
             this.promoMessage = '';
             this.promoValid = false;
             this.promoDiscount = 0;
         }
     }">
    <div class="mb-[48px]">
        <h1 class="text-[32px] md:text-[40px] font-bold text-ink tracking-tight mb-[8px]">Checkout</h1>
        <p class="text-[16px] text-muted">Selesaikan pesanan Anda untuk batch <span class="font-medium text-ink">{{ $batch->title }}</span></p>
    </div>

    <form action="{{ route('food.checkout.process') }}" method="POST">
        @csrf

        <div class="flex flex-col lg:flex-row gap-[48px] lg:gap-[64px]">
            {{-- Left: Form --}}
            <div class="w-full lg:w-[60%] xl:w-[65%] space-y-[48px]">
                
                {{-- 1. Data Pemesan --}}
                <section>
                    <h2 class="text-[22px] font-bold text-ink mb-[24px]">1. Data Pemesan</h2>
                    <div class="space-y-[16px]">
                        <div>
                            <label for="checkout-name" class="block text-[14px] font-medium text-ink mb-[8px]">Nama Lengkap</label>
                            <input type="text" id="checkout-name" name="name" value="{{ old('name') }}" required maxlength="255"
                                   class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas"
                                   placeholder="Nama lengkap Anda">
                            @error('name') <p class="text-primary-error-text text-[13px] mt-[4px]">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="checkout-phone" class="block text-[14px] font-medium text-ink mb-[8px]">No. WhatsApp / HP</label>
                            <input type="tel" id="checkout-phone" name="phone" value="{{ old('phone') }}" required maxlength="20"
                                   class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas"
                                   placeholder="08xxxxxxxxxx">
                            @error('phone') <p class="text-primary-error-text text-[13px] mt-[4px]">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <hr class="border-hairline">

                {{-- 2. Metode Pengiriman --}}
                <section>
                    <h2 class="text-[22px] font-bold text-ink mb-[24px]">2. Metode Pengiriman</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-[16px]">
                        <label class="cursor-pointer group">
                            <input type="radio" name="shipping_method" value="pickup" x-model="shipping" class="sr-only peer" checked>
                            <div class="border border-hairline rounded-[14px] p-[24px] transition-all peer-checked:border-ink peer-checked:bg-surface-soft hover:border-ink">
                                <div class="flex items-center gap-[16px]">
                                    <div class="w-[48px] h-[48px] rounded-full bg-surface-soft flex items-center justify-center text-ink">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-ink text-[16px] mb-[2px]">Ambil Sendiri</p>
                                        <p class="text-[14px] text-muted">Gratis ongkos kirim</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="shipping_method" value="delivery" x-model="shipping" class="sr-only peer">
                            <div class="border border-hairline rounded-[14px] p-[24px] transition-all peer-checked:border-ink peer-checked:bg-surface-soft hover:border-ink">
                                <div class="flex items-center gap-[16px]">
                                    <div class="w-[48px] h-[48px] rounded-full bg-surface-soft flex items-center justify-center text-ink">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-ink text-[16px] mb-[2px]">Delivery</p>
                                        <p class="text-[14px] text-muted">Ongkir dihitung admin</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    {{-- Address (shown for delivery) --}}
                    <div x-show="shipping === 'delivery'" x-transition class="mt-[24px]">
                        <label for="checkout-address" class="block text-[14px] font-medium text-ink mb-[8px]">Alamat Lengkap</label>
                        <textarea id="checkout-address" name="address" rows="3" maxlength="500"
                                  class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas"
                                  placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota">{{ old('address') }}</textarea>
                        @error('address') <p class="text-primary-error-text text-[13px] mt-[4px]">{{ $message }}</p> @enderror
                    </div>
                </section>

                <hr class="border-hairline">

                {{-- 3. Promo & Catatan --}}
                <section>
                    <h2 class="text-[22px] font-bold text-ink mb-[24px]">3. Opsi Tambahan</h2>
                    
                    <div class="mb-[32px]">
                        <label class="block text-[14px] font-medium text-ink mb-[8px]">Kode Promo</label>
                        <div class="flex gap-[12px]">
                            <input type="text" name="promo_code" x-model="promoCode" maxlength="50"
                                   class="flex-1 border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas uppercase"
                                   placeholder="Masukkan kode promo"
                                   :disabled="promoValid">
                            <template x-if="!promoValid">
                                <button type="button" @click="validatePromo()" :disabled="promoLoading || !promoCode.trim()"
                                        class="btn-secondary !h-[auto]">
                                    <span x-show="!promoLoading">Terapkan</span>
                                    <span x-show="promoLoading" class="flex items-center gap-[4px]">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    </span>
                                </button>
                            </template>
                            <template x-if="promoValid">
                                <button type="button" @click="clearPromo()" class="px-[24px] rounded-[8px] bg-red-50 text-red-600 font-medium text-[16px] hover:bg-red-100 transition-colors">
                                    Hapus
                                </button>
                            </template>
                        </div>
                        <div x-show="promoMessage" x-transition class="mt-[12px]">
                            <p :class="promoValid ? 'text-green-700 bg-green-50' : 'text-red-700 bg-red-50'" class="text-[14px] px-[16px] py-[12px] rounded-[8px] font-medium" x-text="promoMessage"></p>
                        </div>
                    </div>

                    <div>
                        <label for="checkout-notes" class="block text-[14px] font-medium text-ink mb-[8px]">Catatan (Opsional)</label>
                        <textarea id="checkout-notes" name="notes" rows="2" maxlength="500"
                                  class="w-full border border-hairline rounded-[8px] px-[16px] py-[12px] text-[16px] text-ink focus:outline-none focus:border-ink transition-colors bg-canvas"
                                  placeholder="Catatan tambahan untuk pesanan Anda">{{ old('notes') }}</textarea>
                    </div>
                </section>
            </div>

            {{-- Right: Order Summary --}}
            <div class="w-full lg:w-[40%] xl:w-[35%]">
                <div class="sticky top-[100px] bg-canvas rounded-[14px] border border-hairline p-[32px] card-shadow-hover">
                    <h2 class="text-[22px] font-bold text-ink mb-[24px]">Ringkasan Pesanan</h2>

                    <div class="space-y-[16px] mb-[32px]">
                        @foreach($cartItems as $item)
                        <div class="flex items-start justify-between text-[16px]">
                            <div class="flex-1 pr-[16px]">
                                <span class="text-ink">{{ $item['product']->name }}</span>
                                <span class="text-muted text-[14px]"> × {{ $item['quantity'] }}</span>
                            </div>
                            <span class="font-medium text-ink shrink-0">Rp {{ number_format($item['line_total'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-hairline pt-[24px] space-y-[12px]">
                        <div class="flex justify-between text-[16px]">
                            <span class="text-muted">Subtotal</span>
                            <span class="font-medium text-ink" x-text="formatRp(subtotal)"></span>
                        </div>
                        <div x-show="promoDiscount > 0" x-transition class="flex justify-between text-[16px]">
                            <span class="text-green-600">Diskon</span>
                            <span class="font-medium text-green-600" x-text="'- ' + formatRp(promoDiscount)"></span>
                        </div>
                        <div class="flex justify-between text-[16px]">
                            <span class="text-muted">Ongkos Kirim</span>
                            <span class="font-medium text-ink" x-text="shipping === 'pickup' ? 'Gratis' : 'Dihitung nanti'"></span>
                        </div>
                    </div>

                    <div class="border-t border-ink mt-[24px] pt-[24px] mb-[32px]">
                        <div class="flex justify-between items-end">
                            <span class="font-bold text-ink text-[16px]">Total</span>
                            <div class="text-right">
                                <span class="text-[24px] font-bold text-ink" x-text="formatRp(total)"></span>
                                <p x-show="shipping === 'delivery'" class="text-[13px] text-muted mt-[4px]">+ Ongkos Kirim</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
                        <template x-if="shipping === 'pickup'">
                            <span>Pesan & Bayar via WhatsApp</span>
                        </template>
                        <template x-if="shipping === 'delivery'">
                            <span>Selesaikan Pesanan</span>
                        </template>
                    </button>

                    <p x-show="shipping === 'delivery'" x-transition class="text-[13px] text-muted text-center mt-[16px]">
                        Admin akan menginformasikan total ongkos kirim.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
