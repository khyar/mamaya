@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card bg-white rounded-2xl p-5 border border-warm-100 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-warm-900">{{ number_format($totalOrders) }}</p>
        <p class="text-xs text-warm-500 mt-1">Total Pesanan</p>
    </div>

    <div class="stat-card bg-white rounded-2xl p-5 border border-warm-100 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-warm-900">{{ number_format($pendingOrders) }}</p>
        <p class="text-xs text-warm-500 mt-1">Perlu Tindakan</p>
    </div>

    <div class="stat-card bg-white rounded-2xl p-5 border border-warm-100 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-warm-900">{{ number_format($completedOrders) }}</p>
        <p class="text-xs text-warm-500 mt-1">Selesai</p>
    </div>

    <div class="stat-card bg-white rounded-2xl p-5 border border-warm-100 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-brand-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-warm-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-warm-500 mt-1">Total Pendapatan</p>
    </div>
</div>

{{-- Active Batches --}}
@if($activeBatches->count() > 0)
<div class="mb-8">
    <h2 class="text-lg font-semibold text-warm-900 mb-4">Batch Aktif</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($activeBatches as $batch)
        <a href="{{ route('admin.batches.show', $batch) }}" class="block bg-white rounded-xl p-4 border border-warm-100 shadow-sm hover:shadow-md hover:border-brand-200 transition-all hover:-translate-y-0.5 group">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-warm-900 text-sm group-hover:text-brand-600 transition-colors">{{ $batch->title }}</h3>
                <span class="w-2 h-2 bg-accent-500 rounded-full animate-pulse"></span>
            </div>
            <div class="flex items-center justify-between text-xs text-warm-500">
                <span>{{ $batch->orders_count }} pesanan</span>
                <span>Tutup: {{ $batch->close_date->format('d M') }}</span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Recent Orders --}}
<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-warm-900">Pesanan Terbaru</h2>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">Lihat Semua →</a>
    </div>

    <div class="bg-white rounded-2xl border border-warm-100 shadow-sm overflow-hidden">
        @if($recentOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-warm-100">
                        <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Order</th>
                        <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Customer</th>
                        <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Batch</th>
                        <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Total</th>
                        <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-warm-50">
                    @foreach($recentOrders as $order)
                    <tr class="hover:bg-warm-50 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                        <td class="px-5 py-3">
                            <span class="text-sm font-semibold text-brand-600">{{ $order->order_number }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-sm font-medium text-warm-900">{{ $order->customer_name }}</p>
                            <p class="text-xs text-warm-400">{{ $order->customer_phone }}</p>
                        </td>
                        <td class="px-5 py-3 text-sm text-warm-600">{{ $order->batch->title ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm font-semibold text-warm-900">{{ $order->formatted_grand_total }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $order->status_color }}">{{ $order->status_label }}</span>
                        </td>
                        <td class="px-5 py-3 text-xs text-warm-400">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-8 text-center text-warm-500">
            <p>Belum ada pesanan.</p>
        </div>
        @endif
    </div>
</div>
@endsection
