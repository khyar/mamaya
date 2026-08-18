<?php

namespace App\Http\Controllers;

use App\Models\JastipTrip;
use App\Services\Checkout\JastipCheckout;
use Illuminate\Http\Request;

class JastipController extends Controller
{
    public function index()
    {
        $trips = JastipTrip::where('is_active', true)->orderBy('departure_date')->get();
        return view('storefront.jastip.index', compact('trips'));
    }

    public function show($slug)
    {
        $trip = JastipTrip::where('slug', $slug)->with('catalogs')->firstOrFail();
        return view('storefront.jastip.show', compact('trip'));
    }

    public function request($slug)
    {
        $trip = JastipTrip::where('slug', $slug)->firstOrFail();
        return view('storefront.jastip.request', compact('trip'));
    }

    public function process(Request $request, JastipCheckout $checkoutStrategy)
    {
        // Add trip_id to request so the validation passes (since it's a hidden field or passed via route)
        $trip = JastipTrip::where('slug', $request->route('slug'))->firstOrFail();
        $request->merge(['trip_id' => $trip->id]);

        $validatedData = $checkoutStrategy->validate($request);
        
        try {
            $order = $checkoutStrategy->process($validatedData);
            return redirect()->route('order.confirmation', $order->order_number)
                ->with('success', 'Request Jastip berhasil dikirim. Kami akan segera memberikan estimasi harga.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.')->withInput();
        }
    }
}
