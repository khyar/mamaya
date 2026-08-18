@extends('layouts.admin')
@section('page-title', 'Edit Campaign')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.campaigns.index') }}" class="inline-flex items-center gap-1 text-sm text-warm-500 hover:text-brand-600 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <div class="bg-white rounded-2xl border border-warm-100 shadow-sm p-6" x-data="{ bgColor: '{{ old('bg_color', $campaign->bg_color ?? '#e97a1e') }}', textColor: '{{ old('text_color', $campaign->text_color ?? '#ffffff') }}', content: '{{ old('content', $campaign->content) }}' }">
        <div class="mb-6">
            <label class="block text-sm font-medium text-warm-700 mb-1.5">Preview</label>
            <div class="py-2.5 px-4 rounded-lg text-sm font-medium text-center" :style="'background-color:' + bgColor + '; color:' + textColor">
                <span x-text="content || 'Teks banner'"></span>
            </div>
        </div>

        <form action="{{ route('admin.campaigns.update', $campaign) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="campaign-title" class="block text-sm font-medium text-warm-700 mb-1.5">Judul (Internal)</label>
                <input type="text" id="campaign-title" name="title" value="{{ old('title', $campaign->title) }}" required maxlength="255"
                       class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="campaign-content" class="block text-sm font-medium text-warm-700 mb-1.5">Teks Banner</label>
                <input type="text" id="campaign-content" name="content" x-model="content" value="{{ old('content', $campaign->content) }}" required maxlength="500"
                       class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="campaign-bg" class="block text-sm font-medium text-warm-700 mb-1.5">Warna Background</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="campaign-bg" name="bg_color" x-model="bgColor" class="w-10 h-10 rounded-lg border border-warm-300 cursor-pointer">
                        <input type="text" x-model="bgColor" maxlength="7" class="flex-1 border border-warm-300 rounded-xl px-3 py-2 text-sm font-mono">
                    </div>
                </div>
                <div>
                    <label for="campaign-text-color" class="block text-sm font-medium text-warm-700 mb-1.5">Warna Teks</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="campaign-text-color" name="text_color" x-model="textColor" class="w-10 h-10 rounded-lg border border-warm-300 cursor-pointer">
                        <input type="text" x-model="textColor" maxlength="7" class="flex-1 border border-warm-300 rounded-xl px-3 py-2 text-sm font-mono">
                    </div>
                </div>
                <div>
                    <label for="campaign-sort" class="block text-sm font-medium text-warm-700 mb-1.5">Urutan</label>
                    <input type="number" id="campaign-sort" name="sort_order" value="{{ old('sort_order', $campaign->sort_order) }}" min="0"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
            </div>

            <div>
                <label for="campaign-link" class="block text-sm font-medium text-warm-700 mb-1.5">Link URL</label>
                <input type="url" id="campaign-link" name="link_url" value="{{ old('link_url', $campaign->link_url) }}" maxlength="255"
                       class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="campaign-start" class="block text-sm font-medium text-warm-700 mb-1.5">Mulai Tayang</label>
                    <input type="datetime-local" id="campaign-start" name="start_date" value="{{ old('start_date', $campaign->start_date?->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
                <div>
                    <label for="campaign-end" class="block text-sm font-medium text-warm-700 mb-1.5">Selesai Tayang</label>
                    <input type="datetime-local" id="campaign-end" name="end_date" value="{{ old('end_date', $campaign->end_date?->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="campaign-active" name="is_active" value="1" {{ old('is_active', $campaign->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-warm-300 text-brand-600 focus:ring-brand-500">
                <label for="campaign-active" class="text-sm text-warm-700">Aktifkan campaign ini</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-brand-500 text-white px-6 py-3 rounded-xl font-semibold text-sm hover:bg-brand-600 transition-colors shadow-sm">Perbarui Campaign</button>
                <a href="{{ route('admin.campaigns.index') }}" class="bg-warm-100 text-warm-700 px-6 py-3 rounded-xl font-semibold text-sm hover:bg-warm-200 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
