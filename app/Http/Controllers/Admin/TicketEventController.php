<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketEventController extends Controller
{
    public function index()
    {
        $events = TicketEvent::latest()->paginate(10);
        return view('admin.tickets.index', compact('events'));
    }

    public function create()
    {
        return view('admin.tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'required|string|max:255',
            'war_start_time' => 'required|date',
            'war_end_time' => 'required|date|after:war_start_time',
            'event_date' => 'required|date|after:war_start_time',
            'is_active' => 'boolean',
            'banner_image' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . time();
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('tickets', 'public');
        }

        TicketEvent::create($validated);

        return redirect()->route('admin.tickets.index')->with('success', 'Event Tiket berhasil ditambahkan.');
    }

    public function show(TicketEvent $ticket) // Actually named $ticket because route is resource('tickets')
    {
        $ticket->load('categories');
        return view('admin.tickets.show', compact('ticket'));
    }

    public function edit(TicketEvent $ticket)
    {
        return view('admin.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, TicketEvent $ticket)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'required|string|max:255',
            'war_start_time' => 'required|date',
            'war_end_time' => 'required|date|after:war_start_time',
            'event_date' => 'required|date|after:war_start_time',
            'is_active' => 'boolean',
            'banner_image' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('tickets', 'public');
        }

        $ticket->update($validated);

        return redirect()->route('admin.tickets.index')->with('success', 'Event Tiket berhasil diperbarui.');
    }

    public function destroy(TicketEvent $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.tickets.index')->with('success', 'Event Tiket berhasil dihapus.');
    }
}
