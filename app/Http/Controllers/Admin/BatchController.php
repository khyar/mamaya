<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::withCount(['orders', 'products'])->latest()->paginate(15);
        return view('admin.batches.index', compact('batches'));
    }

    public function create()
    {
        $products = Product::where('is_available', true)->orderBy('name')->get();
        return view('admin.batches.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'open_date' => ['required', 'date'],
            'close_date' => ['required', 'date', 'after:open_date'],
            'delivery_date' => ['nullable', 'date'],
            'ready_date' => ['nullable', 'date'],
            'is_active' => ['boolean'],
            'products' => ['nullable', 'array'],
            'products.*' => ['integer', 'exists:products,id'],
        ]);

        $batch = Batch::create($validated);

        if (! empty($validated['products'])) {
            $batch->products()->sync($validated['products']);
        }

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch berhasil dibuat.');
    }

    public function show(Batch $batch)
    {
        $batch->loadCount('orders');

        // Estimate total revenue (all non-cancelled orders)
        $estimatedRevenue = $batch->orders()
            ->where('status', '!=', 'cancelled')
            ->sum('grand_total');

        // Confirmed paid revenue (processing, ready, completed)
        $paidRevenue = $batch->orders()
            ->whereIn('status', ['processing', 'ready', 'completed'])
            ->sum('grand_total');

        // Recap orders per product
        $productRecap = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.batch_id', $batch->id)
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_quantity')
            ->get();

        return view('admin.batches.show', compact('batch', 'estimatedRevenue', 'paidRevenue', 'productRecap'));
    }

    public function edit(Batch $batch)
    {
        $products = Product::where('is_available', true)->orderBy('name')->get();
        $selectedProducts = $batch->products->pluck('id')->toArray();
        return view('admin.batches.edit', compact('batch', 'products', 'selectedProducts'));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'open_date' => ['required', 'date'],
            'close_date' => ['required', 'date', 'after:open_date'],
            'delivery_date' => ['nullable', 'date'],
            'ready_date' => ['nullable', 'date'],
            'is_active' => ['boolean'],
            'products' => ['nullable', 'array'],
            'products.*' => ['integer', 'exists:products,id'],
        ]);

        $batch->update($validated);
        $batch->products()->sync($validated['products'] ?? []);

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch berhasil diperbarui.');
    }

    public function destroy(Batch $batch)
    {
        if ($batch->orders()->exists()) {
            return back()->with('error', 'Batch tidak bisa dihapus karena sudah ada pesanan.');
        }

        $batch->delete();

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch berhasil dihapus.');
    }
}
