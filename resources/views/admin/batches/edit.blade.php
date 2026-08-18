@extends('layouts.admin')
@section('page-title', 'Edit Batch PO')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.batches.index') }}" class="inline-flex items-center gap-1 text-sm text-warm-500 hover:text-brand-600 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <div class="bg-white rounded-2xl border border-warm-100 shadow-sm p-6">
        <form action="{{ route('admin.batches.update', $batch) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="batch-title" class="block text-sm font-medium text-warm-700 mb-1.5">Judul Batch</label>
                <input type="text" id="batch-title" name="title" value="{{ old('title', $batch->title) }}" required maxlength="255"
                       class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="batch-description" class="block text-sm font-medium text-warm-700 mb-1.5">Deskripsi</label>
                <textarea id="batch-description" name="description" rows="3" maxlength="1000"
                          class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">{{ old('description', $batch->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="batch-open-date" class="block text-sm font-medium text-warm-700 mb-1.5">Tanggal Buka</label>
                    <input type="datetime-local" id="batch-open-date" name="open_date" value="{{ old('open_date', $batch->open_date->format('Y-m-d\TH:i')) }}" required
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @error('open_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="batch-close-date" class="block text-sm font-medium text-warm-700 mb-1.5">Tanggal Tutup</label>
                    <input type="datetime-local" id="batch-close-date" name="close_date" value="{{ old('close_date', $batch->close_date->format('Y-m-d\TH:i')) }}" required
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @error('close_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="batch-ready-date" class="block text-sm font-medium text-warm-700 mb-1.5">Tanggal Siap</label>
                    <input type="date" id="batch-ready-date" name="ready_date" value="{{ old('ready_date', $batch->ready_date?->format('Y-m-d')) }}"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
                <div>
                    <label for="batch-delivery-date" class="block text-sm font-medium text-warm-700 mb-1.5">Tanggal Kirim</label>
                    <input type="date" id="batch-delivery-date" name="delivery_date" value="{{ old('delivery_date', $batch->delivery_date?->format('Y-m-d')) }}"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
            </div>

            @if($products->count() > 0)
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Pilih Produk</label>
                <div class="border border-warm-200 rounded-xl p-4 max-h-60 overflow-y-auto space-y-2">
                    @foreach($products as $product)
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-warm-50 cursor-pointer">
                        <input type="checkbox" name="products[]" value="{{ $product->id }}"
                               {{ in_array($product->id, old('products', $selectedProducts)) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-warm-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm text-warm-700">{{ $product->name }}</span>
                        <span class="text-xs text-warm-400 ml-auto">{{ $product->formatted_price }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="batch-active" name="is_active" value="1" {{ old('is_active', $batch->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-warm-300 text-brand-600 focus:ring-brand-500">
                <label for="batch-active" class="text-sm text-warm-700">Aktifkan batch ini</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-brand-500 text-white px-6 py-3 rounded-xl font-semibold text-sm hover:bg-brand-600 transition-colors shadow-sm">Perbarui Batch</button>
                <a href="{{ route('admin.batches.index') }}" class="bg-warm-100 text-warm-700 px-6 py-3 rounded-xl font-semibold text-sm hover:bg-warm-200 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
