@extends('layouts.admin')
@section('page-title', 'Edit Produk')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1 text-sm text-warm-500 hover:text-brand-600 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <div class="bg-white rounded-2xl border border-warm-100 shadow-sm p-6">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="product-name" class="block text-sm font-medium text-warm-700 mb-1.5">Nama Produk</label>
                <input type="text" id="product-name" name="name" value="{{ old('name', $product->name) }}" required maxlength="255"
                       class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="product-description" class="block text-sm font-medium text-warm-700 mb-1.5">Deskripsi</label>
                <textarea id="product-description" name="description" rows="4" maxlength="2000"
                          class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="product-price" class="block text-sm font-medium text-warm-700 mb-1.5">Harga (Rp)</label>
                    <input type="number" id="product-price" name="price" value="{{ old('price', $product->price) }}" required min="0" step="500"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="product-sort" class="block text-sm font-medium text-warm-700 mb-1.5">Urutan Tampil</label>
                    <input type="number" id="product-sort" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" min="0"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
            </div>

            {{-- Current Image --}}
            @if($product->image)
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Foto Utama Saat Ini</label>
                <div class="w-32 h-32 rounded-xl overflow-hidden bg-warm-100">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
            </div>
            @endif

            <div>
                <label for="product-image" class="block text-sm font-medium text-warm-700 mb-1.5">Ganti Foto Utama</label>
                <input type="file" id="product-image" name="image" accept="image/jpeg,image/png,image/webp"
                       class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                <p class="text-xs text-warm-400 mt-1">Kosongkan jika tidak ingin mengubah</p>
                @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Current Gallery --}}
            @if($product->images->count() > 0)
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Galeri Saat Ini</label>
                <div class="flex flex-wrap gap-3">
                    @foreach($product->images as $image)
                    <div class="relative group">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-warm-100">
                            <img src="{{ $image->url }}" alt="Gallery" class="w-full h-full object-cover">
                        </div>
                        <label class="absolute top-1 right-1 bg-red-500 text-white w-5 h-5 rounded-full flex items-center justify-center text-xs cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity" title="Hapus">
                            <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="sr-only">
                            ×
                        </label>
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-warm-400 mt-1">Klik × untuk menandai foto yang akan dihapus</p>
            </div>
            @endif

            <div>
                <label for="product-gallery" class="block text-sm font-medium text-warm-700 mb-1.5">Tambah Foto Galeri</label>
                <input type="file" id="product-gallery" name="gallery[]" accept="image/jpeg,image/png,image/webp" multiple
                       class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_available" value="0">
                <input type="checkbox" id="product-available" name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-warm-300 text-brand-600 focus:ring-brand-500">
                <label for="product-available" class="text-sm text-warm-700">Produk tersedia</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-brand-500 text-white px-6 py-3 rounded-xl font-semibold text-sm hover:bg-brand-600 transition-colors shadow-sm">Perbarui Produk</button>
                <a href="{{ route('admin.products.index') }}" class="bg-warm-100 text-warm-700 px-6 py-3 rounded-xl font-semibold text-sm hover:bg-warm-200 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
