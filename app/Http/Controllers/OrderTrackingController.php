<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function __construct(
        private WhatsAppService $whatsAppService,
    ) {}

    /**
     * Show the tracking form.
     */
    public function show()
    {
        return view('storefront.track-order');
    }

    /**
     * Look up order by order number + phone.
     */
    public function track(Request $request)
    {
        $request->validate([
            'order_number' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->where('customer_phone', $request->phone)
            ->with(['items', 'foodDetail.batch', 'ticketDetail.event', 'jastipDetail.trip'])
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->with('error', 'Order tidak ditemukan. Periksa kembali No. Order dan No. HP Anda.');
        }

        $whatsappLink = null;
        if ($order->canShowPaymentLink()) {
            $whatsappLink = $this->whatsAppService->generatePaymentLink($order);
        }

        return view('storefront.track-order', compact('order', 'whatsappLink'));
    }
}
