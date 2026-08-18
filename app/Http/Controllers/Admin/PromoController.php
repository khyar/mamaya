<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::with('batch')->latest()->paginate(15);
        return view('admin.promos.index', compact('promos'));
    }

    public function create()
    {
        $batches = Batch::orderBy('created_at', 'desc')->get();
        return view('admin.promos.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promos,code'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'batch_id' => ['nullable', 'integer', 'exists:batches,id'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));

        Promo::create($validated);

        return redirect()->route('admin.promos.index')
            ->with('success', 'Kode promo berhasil dibuat.');
    }

    public function edit(Promo $promo)
    {
        $batches = Batch::orderBy('created_at', 'desc')->get();
        return view('admin.promos.edit', compact('promo', 'batches'));
    }

    public function update(Request $request, Promo $promo)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promos,code,' . $promo->id],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'batch_id' => ['nullable', 'integer', 'exists:batches,id'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));

        $promo->update($validated);

        return redirect()->route('admin.promos.index')
            ->with('success', 'Kode promo berhasil diperbarui.');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();

        return redirect()->route('admin.promos.index')
            ->with('success', 'Kode promo berhasil dihapus.');
    }
}
