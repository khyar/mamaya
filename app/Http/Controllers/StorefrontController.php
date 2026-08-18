<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    /**
     * Super App Portal: Hub for Food, Tickets, and Jastip.
     */
    public function portal()
    {
        return view('storefront.portal');
    }

    /**
     * Homepage: active banners, active batch info, featured products.
     */
    public function index()
    {
        $campaigns = Campaign::currentlyActive()->get();
        $activeBatches = Batch::open()->get();

        // Get products for the first open batch (if any)
        $products = collect();
        $selectedBatch = $activeBatches->first();
        if ($selectedBatch) {
            $products = $selectedBatch->products()
                ->where('is_available', true)
                ->orderBy('sort_order')
                ->with('images')
                ->take(8)
                ->get();
        }

        return view('storefront.home', compact('campaigns', 'activeBatches', 'products', 'selectedBatch'));
    }

    /**
     * Products page with batch selector.
     */
    public function products(Request $request)
    {
        $activeBatches = Batch::open()->get();
        $selectedBatchId = $request->query('batch');

        $selectedBatch = null;
        $products = collect();

        if ($selectedBatchId) {
            $selectedBatch = Batch::find($selectedBatchId);
        }

        if (! $selectedBatch && $activeBatches->isNotEmpty()) {
            $selectedBatch = $activeBatches->first();
        }

        if ($selectedBatch) {
            $products = $selectedBatch->products()
                ->where('is_available', true)
                ->orderBy('sort_order')
                ->with('images')
                ->get();
        }

        return view('storefront.products', compact('activeBatches', 'products', 'selectedBatch'));
    }

    /**
     * Single product detail.
     */
    public function productDetail(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_available', true)
            ->with('images')
            ->firstOrFail();

        $activeBatches = Batch::open()
            ->whereHas('products', fn ($q) => $q->where('products.id', $product->id))
            ->get();

        return view('storefront.product-detail', compact('product', 'activeBatches'));
    }
}
