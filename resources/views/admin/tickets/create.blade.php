@extends('layouts.admin')
@section('title', 'Tambah Event Tiket')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center gap-1 text-sm text-warm-500 hover:text-brand-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-warm-100 overflow-hidden">
    <div class="p-6 border-b border-warm-100">
        <h2 class="text-xl font-bold text-warm-900">Tambah Event Baru</h2>
    </div>
    
    <form action="{{ route('admin.tickets.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-2">Nama Event *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-2">Venue / Lokasi *</label>
                <input type="text" name="venue" value="{{ old('venue') }}" required class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                @error('venue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-2">Tanggal Event *</label>
                <input type="date" name="event_date" value="{{ old('event_date') }}" required class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                @error('event_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-2">Mulai War *</label>
                <input type="datetime-local" name="war_start_time" value="{{ old('war_start_time') }}" required class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                @error('war_start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-2">Selesai War *</label>
                <input type="datetime-local" name="war_end_time" value="{{ old('war_end_time') }}" required class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                @error('war_end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-warm-700 mb-2">Deskripsi Lengkap (Syarat & Ketentuan)</label>
            <textarea name="description" rows="5" class="w-full border-warm-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 rounded border-warm-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm font-medium text-warm-900">Publikasikan Event Ini</span>
            </label>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-warm-100">
            <a href="{{ route('admin.tickets.index') }}" class="px-6 py-2.5 rounded-xl text-warm-700 font-semibold hover:bg-warm-100 transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white rounded-xl font-semibold hover:bg-brand-700 transition-colors shadow-sm">
                Simpan Event
            </button>
        </div>
    </form>
</div>
@endsection
