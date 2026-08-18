@extends('layouts.admin')
@section('title', 'Rekap Batch: ' . $batch->title)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.batches.index') }}" class="text-warm-500 hover:text-brand-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-warm-900">Rekap Batch</h1>
        </div>
        <p class="text-sm text-warm-500">{{ $batch->title }}</p>
    </div>
    
    <div class="flex items-center gap-3">
        @if($batch->is_active)
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold border border-green-200 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                Aktif
            </span>
        @else
            <span class="px-3 py-1 bg-warm-100 text-warm-700 rounded-lg text-xs font-semibold border border-warm-200">Non-Aktif</span>
        @endif
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-warm-100">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">Total Pesanan</p>
                <h3 class="text-2xl font-bold text-warm-900">{{ $batch->orders_count }}</h3>
            </div>
        </div>
        <p class="text-xs text-warm-500">Jumlah pesanan (tidak termasuk dibatalkan)</p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-warm-100">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-brand-50 text-brand-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">Estimasi Pemasukan</p>
                <h3 class="text-2xl font-bold text-warm-900">Rp {{ number_format($estimatedRevenue, 0, ',', '.') }}</h3>
            </div>
        </div>
        <p class="text-xs text-warm-500">Potensi total dari semua pesanan aktif</p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-warm-100">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">Pendapatan Diterima</p>
                <h3 class="text-2xl font-bold text-warm-900">Rp {{ number_format($paidRevenue, 0, ',', '.') }}</h3>
            </div>
        </div>
        <p class="text-xs text-warm-500">Hanya pesanan yang sudah dibayar (Processing/Ready/Completed)</p>
    </div>
</div>

{{-- Product Recap Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-warm-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-warm-100 bg-warm-50/50">
        <h3 class="font-semibold text-warm-900">Rekap Produk Dipesan</h3>
        <p class="text-sm text-warm-500 mt-1">Total rincian per produk untuk keperluan dapur.</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-warm-600">
            <thead class="bg-warm-50/50 text-warm-500 font-medium">
                <tr>
                    <th class="px-6 py-4">Nama Produk</th>
                    <th class="px-6 py-4 text-center">Total Kuantitas</th>
                    <th class="px-6 py-4 text-right">Subtotal Omset</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-100">
                @forelse($productRecap as $item)
                <tr class="hover:bg-warm-50/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-warm-900">{{ $item->product_name }}</td>
                    <td class="px-6 py-4 text-center font-bold text-brand-600">{{ $item->total_quantity }}</td>
                    <td class="px-6 py-4 text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-warm-500">
                        Belum ada pesanan untuk batch ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
