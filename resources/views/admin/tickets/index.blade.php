@extends('layouts.admin')
@section('title', 'Manajemen Tiket Event')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-warm-900">Tiket Event</h1>
        <p class="text-sm text-warm-500 mt-1">Kelola event konser, workshop, dll.</p>
    </div>
    <a href="{{ route('admin.tickets.create') }}" class="bg-brand-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-brand-700 transition-colors shadow-sm flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Event
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-warm-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-warm-50 border-b border-warm-100 text-warm-600 font-semibold">
                <tr>
                    <th class="px-6 py-4">Nama Event</th>
                    <th class="px-6 py-4">Tanggal Event</th>
                    <th class="px-6 py-4">Status War</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-100">
                @forelse($events as $event)
                <tr class="hover:bg-warm-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-warm-900">{{ $event->name }}</div>
                        <div class="text-xs text-warm-500">{{ $event->venue }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-warm-600">
                        {{ $event->event_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($event->isWarActive())
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">🔥 Aktif</span>
                        @elseif(now()->isBefore($event->war_start_time))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-warm-100 text-warm-800 text-xs font-semibold">Belum Mulai</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold">Selesai</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <button class="text-brand-600 hover:text-brand-800 font-medium">Edit</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-warm-500">Belum ada event tiket yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($events->hasPages())
    <div class="px-6 py-4 border-t border-warm-100">
        {{ $events->links() }}
    </div>
    @endif
</div>
@endsection
