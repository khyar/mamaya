@extends('layouts.admin')
@section('title', 'Tambah Trip Jastip')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.jastips.index') }}" class="inline-flex items-center gap-1 text-sm text-warm-500 hover:text-brand-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-warm-100 overflow-hidden">
    <div class="p-6 border-b border-warm-100">
        <h2 class="text-xl font-bold text-warm-900">Tambah Trip Jastip Baru</h2>
    </div>
    
    <form action="{{ route('admin.jastips.store') }}" method="POST" class="p-6 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-2">Destinasi (Negara / Kota) *</label>
                <input type="text" name="destination" value="{{ old('destination') }}" required class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500" placeholder="Contoh: Jepang (Tokyo & Osaka)">
                @error('destination') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-2">Kuota Bagasi Tersedia (KG) *</label>
                <input type="number" name="baggage_quota_kg" value="{{ old('baggage_quota_kg', 0) }}" step="0.5" required class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                @error('baggage_quota_kg') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-2">Batas Akhir PO *</label>
                <input type="date" name="po_close_date" value="{{ old('po_close_date') }}" required class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                @error('po_close_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-2">Tanggal Berangkat *</label>
                <input type="date" name="departure_date" value="{{ old('departure_date') }}" required class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                @error('departure_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-2">Tanggal Pulang (Pengiriman) *</label>
                <input type="date" name="return_date" value="{{ old('return_date') }}" required class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                @error('return_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-warm-700 mb-2">Deskripsi / Syarat Ketentuan Titip</label>
            <textarea name="description" rows="5" class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 rounded border-warm-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm font-medium text-warm-900">Publikasikan Trip Ini</span>
            </label>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-warm-100">
            <a href="{{ route('admin.jastips.index') }}" class="px-6 py-2.5 rounded-xl text-warm-700 font-semibold hover:bg-warm-100 transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white rounded-xl font-semibold hover:bg-brand-700 transition-colors shadow-sm">
                Simpan Trip
            </button>
        </div>
    </form>
</div>
@endsection
