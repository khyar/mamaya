<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private WhatsAppService $whatsAppService,
    ) {}

    public function index(Request $request)
    {
        $query = Order::with(['foodDetail.batch', 'ticketDetail.event', 'jastipDetail.trip', 'items']);

        // Filter by batch (Food only for now)
        if ($request->filled('batch_id')) {
            $query->whereHas('foodDetail', function($q) use ($request) {
                $q->where('batch_id', $request->batch_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();
        $batches = Batch::orderBy('created_at', 'desc')->get();

        $statuses = [
            'pending' => 'Menunggu',
            'awaiting_shipping_cost' => 'Menunggu Ongkir',
            'awaiting_payment' => 'Menunggu Pembayaran',
            'processing' => 'Diproses',
            'ready' => 'Siap',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return view('admin.orders.index', compact('orders', 'batches', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load(['foodDetail.batch', 'ticketDetail.event', 'jastipDetail.trip', 'items', 'promo']);

        $whatsappLink = null;
        if ($order->canShowPaymentLink()) {
            $whatsappLink = $this->whatsAppService->generatePaymentLink($order);
        }

        $statuses = [
            'pending' => 'Menunggu',
            'awaiting_shipping_cost' => 'Menunggu Ongkir',
            'awaiting_payment' => 'Menunggu Pembayaran',
            'processing' => 'Diproses',
            'ready' => 'Siap',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return view('admin.orders.show', compact('order', 'whatsappLink', 'statuses'));
    }

    /**
     * Set shipping cost for a delivery order.
     */
    public function setShippingCost(Request $request, Order $order)
    {
        $request->validate([
            'shipping_cost' => ['required', 'numeric', 'min:0', 'max:999999'],
        ]);

        if ($order->order_type !== 'food' || !$order->foodDetail || $order->foodDetail->shipping_method !== 'delivery') {
            return back()->with('error', 'Order ini bukan pengiriman delivery untuk makanan.');
        }

        if (! in_array($order->status, ['pending', 'awaiting_shipping_cost'])) {
            return back()->with('error', 'Ongkir hanya bisa diisi untuk order yang menunggu ongkir.');
        }

        $shippingCost = (float) $request->shipping_cost;
        
        $order->foodDetail->update(['shipping_cost' => $shippingCost]);
        
        $afterDiscount = (float) $order->subtotal - (float) $order->discount_amount;
        $order->update([
            'grand_total' => $afterDiscount + $shippingCost,
            'status' => 'awaiting_payment',
        ]);

        return back()->with('success', 'Ongkir berhasil diisi. Status order diubah ke Menunggu Pembayaran.');
    }

    /**
     * Set quotation price for a Jastip custom request.
     */
    public function setJastipQuotation(Request $request, Order $order)
    {
        $request->validate([
            'quotation_price' => ['required', 'numeric', 'min:0'],
        ]);

        if ($order->order_type !== 'jastip' || !$order->jastipDetail) {
            return back()->with('error', 'Order ini bukan pesanan jastip.');
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Penawaran harga hanya bisa diisi untuk order dengan status Menunggu.');
        }

        $quotationPrice = (float) $request->quotation_price;
        
        $order->update([
            'subtotal' => $quotationPrice,
            'grand_total' => $quotationPrice, // no separate shipping or promo for now
            'status' => 'awaiting_payment',
        ]);

        return back()->with('success', 'Penawaran harga jastip berhasil dikirim. Status order diubah ke Menunggu Pembayaran.');
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,awaiting_shipping_cost,awaiting_payment,processing,ready,completed,cancelled'],
        ]);

        $newStatus = $request->status;

        // If marking as processing, set paid_at
        if ($newStatus === 'processing' && ! $order->paid_at) {
            $order->paid_at = now();
        }

        $order->status = $newStatus;
        $order->save();

        return back()->with('success', 'Status order berhasil diperbarui.');
    }
}
