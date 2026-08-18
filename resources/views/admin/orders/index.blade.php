@extends('layouts.admin')
@section('page-title', 'Pesanan')

@section('content')
{{-- Filters --}}
<div class="bg-white rounded-2xl border border-warm-100 shadow-sm p-4 mb-6">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-warm-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Order / Nama / HP"
                   class="w-full border border-warm-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-warm-500 mb-1">Batch</label>
            <select name="batch_id" class="border border-warm-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                <option value="">Semua Batch</option>
                @foreach($batches as $batch)
                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-warm-500 mb-1">Status</label>
            <select name="status" class="border border-warm-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                <option value="">Semua Status</option>
                @foreach($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-warm-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-warm-800 transition-colors">Filter</button>
        @if(request()->hasAny(['search', 'batch_id', 'status']))
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-warm-500 hover:text-brand-600 px-3 py-2">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl border border-warm-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-warm-100">
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Order</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Customer</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Tipe Pesanan / Layanan</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Kirim</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Total</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Waktu</th>
                    <th class="text-right text-xs font-semibold text-warm-500 uppercase tracking-wider px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-50">
                @forelse($orders as $order)
                <tr class="hover:bg-warm-50 transition-colors {{ $order->status === 'awaiting_shipping_cost' ? 'bg-orange-50/50' : '' }}">
                    <td class="px-5 py-3">
                        <span class="text-sm font-semibold text-brand-600">{{ $order->order_number }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-sm font-medium text-warm-900">{{ $order->customer_name }}</p>
                        <p class="text-xs text-warm-400">{{ $order->customer_phone }}</p>
                    </td>
                    <td class="px-5 py-3 text-sm text-warm-600">
                        @if($order->order_type === 'food')
                            <span class="font-medium">🍔 Food PO</span><br>
                            <span class="text-xs">{{ $order->foodDetail->batch->title ?? '-' }}</span>
                        @elseif($order->order_type === 'ticket')
                            <span class="font-medium">🎫 Tiket</span><br>
                            <span class="text-xs">{{ $order->ticketDetail->event->name ?? '-' }}</span>
                        @elseif($order->order_type === 'jastip')
                            <span class="font-medium">✈️ Jastip</span><br>
                            <span class="text-xs">{{ $order->jastipDetail->trip->destination ?? '-' }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if($order->order_type === 'food')
                            <span class="text-xs px-2 py-0.5 rounded {{ optional($order->foodDetail)->shipping_method === 'pickup' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ optional($order->foodDetail)->shipping_method === 'pickup' ? '🏪 Pickup' : '🚚 Delivery' }}
                            </span>
                        @elseif($order->order_type === 'ticket')
                            <span class="text-xs px-2 py-0.5 rounded bg-green-100 text-green-700">📧 E-Ticket</span>
                        @elseif($order->order_type === 'jastip')
                            <span class="text-xs px-2 py-0.5 rounded bg-purple-100 text-purple-700">📦 Kirim Paket</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-sm font-semibold text-warm-900">{{ $order->formatted_grand_total }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $order->status_color }}">{{ $order->status_label }}</span>
                    </td>
                    <td class="px-5 py-3 text-xs text-warm-400">{{ $order->created_at->format('d/m H:i') }}</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-brand-600 hover:text-brand-700 text-sm font-medium">Detail →</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-warm-400">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $orders->links() }}</div>
@endsection
