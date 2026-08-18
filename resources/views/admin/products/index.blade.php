@extends('layouts.admin')
@section('page-title', 'Produk')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-warm-500 text-sm">Kelola menu produk</p>
    <a href="{{ route('admin.products.create') }}" class="bg-brand-500 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-brand-600 transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Tambah Produk
    </a>
</div>

<div class="bg-white rounded-2xl border border-warm-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-warm-100">
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Produk</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Harga</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Urutan</th>
                    <th class="text-right text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-50">
                @forelse($products as $product)
                <tr class="hover:bg-warm-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-warm-100 flex-shrink-0">
                                <img src="{{ $product->primaryImageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-warm-900">{{ $product->name }}</p>
                                <p class="text-xs text-warm-400">{{ $product->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm font-semibold text-brand-600">{{ $product->formatted_price }}</td>
                    <td class="px-5 py-3">
                        @if($product->is_available)
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Tersedia</span>
                        @else
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-sm text-warm-600">{{ $product->sort_order }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="p-2 text-warm-400 hover:text-brand-600 rounded-lg hover:bg-brand-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-warm-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-warm-400">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $products->links() }}</div>
@endsection
