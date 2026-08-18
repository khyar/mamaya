<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $activeBatches = Batch::open()->withCount('orders')->get();
        $totalOrders = Order::count();
        $pendingOrders = Order::whereIn('status', ['pending', 'awaiting_shipping_cost', 'awaiting_payment'])->count();
        $processingOrders = Order::whereIn('status', ['processing', 'ready'])->count();
        $completedOrders = Order::where('status', 'completed')->count();

        $recentOrders = Order::with(['batch', 'items'])
            ->latest()
            ->take(10)
            ->get();

        // Revenue from completed + processing orders
        $totalRevenue = Order::whereIn('status', ['processing', 'ready', 'completed'])
            ->whereNotNull('grand_total')
            ->sum('grand_total');

        return view('admin.dashboard', compact(
            'activeBatches',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'recentOrders',
            'totalRevenue',
        ));
    }
}
