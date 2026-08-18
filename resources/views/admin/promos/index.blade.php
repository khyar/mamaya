@extends('layouts.admin')
@section('page-title', 'Kode Promo')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-warm-500 text-sm">Kelola kode promo dan diskon</p>
    <a href="{{ route('admin.promos.create') }}" class="bg-brand-500 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-brand-600 transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Tambah Promo
    </a>
</div>

<div class="bg-white rounded-2xl border border-warm-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-warm-100">
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Kode</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Tipe</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Nilai</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Batch</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Penggunaan</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-right text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-50">
                @forelse($promos as $promo)
                <tr class="hover:bg-warm-50 transition-colors">
                    <td class="px-5 py-3">
                        <span class="font-mono text-sm font-bold text-brand-600 bg-brand-50 px-2 py-1 rounded">{{ $promo->code }}</span>
                    </td>
                    <td class="px-5 py-3 text-sm text-warm-600">
                        {{ $promo->type === 'fixed' ? 'Potongan' : 'Persentase' }}
                    </td>
                    <td class="px-5 py-3 text-sm font-semibold text-warm-900">
                        @if($promo->type === 'fixed')
                        Rp {{ number_format((float)$promo->value, 0, ',', '.') }}
                        @else
                        {{ (int)$promo->value }}%
                        @if($promo->max_discount)
                        <span class="text-xs text-warm-400">(maks Rp {{ number_format((float)$promo->max_discount, 0, ',', '.') }})</span>
                        @endif
                        @endif
                    </td>
                    <td class="px-5 py-3 text-sm text-warm-600">{{ $promo->batch?->title ?? 'Semua' }}</td>
                    <td class="px-5 py-3 text-sm text-warm-600">{{ $promo->used_count }}{{ $promo->max_uses ? '/' . $promo->max_uses : '' }}</td>
                    <td class="px-5 py-3">
                        @if($promo->is_active)
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Aktif</span>
                        @else
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.promos.edit', $promo) }}" class="p-2 text-warm-400 hover:text-brand-600 rounded-lg hover:bg-brand-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" onsubmit="return confirm('Hapus promo ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-warm-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-warm-400">Belum ada kode promo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $promos->links() }}</div>
@endsection
