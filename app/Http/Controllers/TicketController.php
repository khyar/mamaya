<?php

namespace App\Http\Controllers;

use App\Models\TicketEvent;
use App\Services\Checkout\TicketCheckout;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $events = TicketEvent::where('is_active', true)->orderBy('event_date')->get();
        return view('storefront.tickets.index', compact('events'));
    }

    public function show($slug)
    {
        $event = TicketEvent::where('slug', $slug)->with('categories')->firstOrFail();
        return view('storefront.tickets.show', compact('event'));
    }

    public function checkout($slug)
    {
        $event = TicketEvent::where('slug', $slug)->with('categories')->firstOrFail();
        
        if (!$event->isWarActive()) {
            return redirect()->route('tickets.show', $event->slug)
                ->with('error', 'Penjualan tiket belum dibuka atau sudah ditutup.');
        }

        return view('storefront.tickets.checkout', compact('event'));
    }

    public function process(Request $request, TicketCheckout $checkoutStrategy)
    {
        $validatedData = $checkoutStrategy->validate($request);
        
        try {
            $order = $checkoutStrategy->process($validatedData);
            return redirect()->route('order.confirmation', $order->order_number)
                ->with('success', 'Booking berhasil! Segera lakukan pembayaran.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.')->withInput();
        }
    }
}
