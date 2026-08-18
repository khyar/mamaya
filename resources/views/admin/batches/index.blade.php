@extends('layouts.admin')
@section('page-title', 'Batch PO')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-warm-500 text-sm">Kelola batch pre-order</p>
    <a href="{{ route('admin.batches.create') }}" class="bg-brand-500 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-brand-600 transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Tambah Batch
    </a>
</div>

<div class="bg-white rounded-2xl border border-warm-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-warm-100">
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Batch</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Buka</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Tutup</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Produk</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Pesanan</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-right text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-50">
                @forelse($batches as $batch)
                <tr class="hover:bg-warm-50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="text-sm font-semibold text-warm-900">{{ $batch->title }}</p>
                        @if($batch->ready_date)
                        <p class="text-xs text-warm-400">Siap: {{ $batch->ready_date->format('d M Y') }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-sm text-warm-600">{{ $batch->open_date->format('d M Y, H:i') }}</td>
                    <td class="px-5 py-3 text-sm text-warm-600">{{ $batch->close_date->format('d M Y, H:i') }}</td>
                    <td class="px-5 py-3 text-sm text-warm-600">{{ $batch->products_count }}</td>
                    <td class="px-5 py-3 text-sm text-warm-600">{{ $batch->orders_count }}</td>
                    <td class="px-5 py-3">
                        @if($batch->isOpen())
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Aktif</span>
                        @elseif($batch->is_active)
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Terjadwal</span>
                        @else
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.batches.show', $batch) }}" title="Lihat Rekap" class="p-2 text-warm-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.batches.edit', $batch) }}" title="Edit" class="p-2 text-warm-400 hover:text-brand-600 rounded-lg hover:bg-brand-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.batches.destroy', $batch) }}" method="POST" onsubmit="return confirm('Hapus batch ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-warm-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-warm-400">Belum ada batch.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $batches->links() }}</div>
@endsection
