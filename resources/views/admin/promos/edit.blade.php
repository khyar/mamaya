@extends('layouts.admin')
@section('page-title', 'Edit Kode Promo')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.promos.index') }}" class="inline-flex items-center gap-1 text-sm text-warm-500 hover:text-brand-600 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <div class="bg-white rounded-2xl border border-warm-100 shadow-sm p-6">
        <form action="{{ route('admin.promos.update', $promo) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="promo-code" class="block text-sm font-medium text-warm-700 mb-1.5">Kode Promo</label>
                <input type="text" id="promo-code" name="code" value="{{ old('code', $promo->code) }}" required maxlength="50"
                       class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 uppercase font-mono">
                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="promo-type" class="block text-sm font-medium text-warm-700 mb-1.5">Tipe Diskon</label>
                    <select id="promo-type" name="type" required class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        <option value="fixed" {{ old('type', $promo->type) === 'fixed' ? 'selected' : '' }}>Potongan Tetap (Rp)</option>
                        <option value="percentage" {{ old('type', $promo->type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                </div>
                <div>
                    <label for="promo-value" class="block text-sm font-medium text-warm-700 mb-1.5">Nilai</label>
                    <input type="number" id="promo-value" name="value" value="{{ old('value', $promo->value) }}" required min="0" step="0.01"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @error('value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="promo-min-order" class="block text-sm font-medium text-warm-700 mb-1.5">Min. Order (Rp)</label>
                    <input type="number" id="promo-min-order" name="min_order" value="{{ old('min_order', $promo->min_order) }}" min="0"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
                <div>
                    <label for="promo-max-discount" class="block text-sm font-medium text-warm-700 mb-1.5">Maks. Diskon (Rp)</label>
                    <input type="number" id="promo-max-discount" name="max_discount" value="{{ old('max_discount', $promo->max_discount) }}" min="0"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="promo-start-date" class="block text-sm font-medium text-warm-700 mb-1.5">Mulai Berlaku</label>
                    <input type="datetime-local" id="promo-start-date" name="start_date" value="{{ old('start_date', $promo->start_date?->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
                <div>
                    <label for="promo-end-date" class="block text-sm font-medium text-warm-700 mb-1.5">Berlaku Sampai</label>
                    <input type="datetime-local" id="promo-end-date" name="end_date" value="{{ old('end_date', $promo->end_date?->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="promo-batch" class="block text-sm font-medium text-warm-700 mb-1.5">Khusus Batch</label>
                    <select id="promo-batch" name="batch_id" class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        <option value="">Semua Batch</option>
                        @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" {{ old('batch_id', $promo->batch_id) == $batch->id ? 'selected' : '' }}>{{ $batch->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="promo-max-uses" class="block text-sm font-medium text-warm-700 mb-1.5">Maks. Penggunaan</label>
                    <input type="number" id="promo-max-uses" name="max_uses" value="{{ old('max_uses', $promo->max_uses) }}" min="1"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
            </div>

            <div class="bg-warm-50 p-3 rounded-lg">
                <p class="text-xs text-warm-500">Sudah digunakan: <strong>{{ $promo->used_count }} kali</strong></p>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="promo-active" name="is_active" value="1" {{ old('is_active', $promo->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-warm-300 text-brand-600 focus:ring-brand-500">
                <label for="promo-active" class="text-sm text-warm-700">Aktifkan promo ini</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-brand-500 text-white px-6 py-3 rounded-xl font-semibold text-sm hover:bg-brand-600 transition-colors shadow-sm">Perbarui Promo</button>
                <a href="{{ route('admin.promos.index') }}" class="bg-warm-100 text-warm-700 px-6 py-3 rounded-xl font-semibold text-sm hover:bg-warm-200 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
