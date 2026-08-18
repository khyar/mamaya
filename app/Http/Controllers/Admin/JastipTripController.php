<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JastipTrip;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JastipTripController extends Controller
{
    public function index()
    {
        $trips = JastipTrip::latest()->paginate(10);
        return view('admin.jastip.index', compact('trips'));
    }

    public function create()
    {
        return view('admin.jastip.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'po_close_date' => 'required|date|before:departure_date',
            'baggage_quota_kg' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['destination']) . '-' . time();
        $validated['is_active'] = $request->has('is_active');

        JastipTrip::create($validated);

        return redirect()->route('admin.jastip.index')->with('success', 'Trip Jastip berhasil ditambahkan.');
    }

    public function show(JastipTrip $jastip) // route resource uses jastips, so variable is $jastip
    {
        $jastip->load('catalogs');
        return view('admin.jastip.show', compact('jastip'));
    }

    public function edit(JastipTrip $jastip)
    {
        return view('admin.jastip.edit', compact('jastip'));
    }

    public function update(Request $request, JastipTrip $jastip)
    {
        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'po_close_date' => 'required|date|before:departure_date',
            'baggage_quota_kg' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $jastip->update($validated);

        return redirect()->route('admin.jastip.index')->with('success', 'Trip Jastip berhasil diperbarui.');
    }

    public function destroy(JastipTrip $jastip)
    {
        $jastip->delete();
        return redirect()->route('admin.jastip.index')->with('success', 'Trip Jastip berhasil dihapus.');
    }
}
