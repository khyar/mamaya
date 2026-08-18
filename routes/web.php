<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\TicketEventController;
use App\Http\Controllers\Admin\JastipTripController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\JastipController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Storefront Routes
|--------------------------------------------------------------------------
*/

// Super App Portal
Route::get('/', [StorefrontController::class, 'portal'])->name('home');

/*
|--------------------------------------------------------------------------
| Food Service Routes
|--------------------------------------------------------------------------
*/
Route::prefix('food')->name('food.')->group(function () {
    Route::get('/', [StorefrontController::class, 'index'])->name('home');
    Route::get('/products', [StorefrontController::class, 'products'])->name('products');
    Route::get('/products/{slug}', [StorefrontController::class, 'productDetail'])->name('product.detail');

    // Cart
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/update', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{productId}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    });

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout/validate-promo', [CheckoutController::class, 'validatePromo'])->name('checkout.validate-promo');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
});

/*
|--------------------------------------------------------------------------
| Ticket Service Routes
|--------------------------------------------------------------------------
*/
Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('index');
    Route::get('/{slug}', [TicketController::class, 'show'])->name('show');
    Route::get('/{slug}/checkout', [TicketController::class, 'checkout'])->name('checkout');
    Route::post('/{slug}/checkout', [TicketController::class, 'process'])->name('checkout.process');
});

/*
|--------------------------------------------------------------------------
| Jastip Service Routes
|--------------------------------------------------------------------------
*/
Route::prefix('jastip')->name('jastip.')->group(function () {
    Route::get('/', [JastipController::class, 'index'])->name('index');
    Route::get('/{slug}', [JastipController::class, 'show'])->name('show');
    Route::get('/{slug}/request', [JastipController::class, 'request'])->name('request');
    Route::post('/{slug}/request', [JastipController::class, 'process'])->name('request.process');
});

// Universal Order Confirmation & Tracking
Route::get('/order/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('order.confirmation');
Route::get('/track', [OrderTrackingController::class, 'show'])->name('track.show');
Route::post('/track', [OrderTrackingController::class, 'track'])->name('track.search');

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin Dashboard Routes (Auth Protected)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Batches
    Route::resource('batches', BatchController::class);

    // Products
    Route::resource('products', ProductController::class)->except(['show']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/shipping-cost', [OrderController::class, 'setShippingCost'])->name('orders.shipping-cost');
    Route::post('orders/{order}/jastip-quotation', [OrderController::class, 'setJastipQuotation'])->name('orders.jastip-quotation');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Promos
    Route::resource('promos', PromoController::class)->except(['show']);

    // Campaigns
    Route::resource('campaigns', CampaignController::class)->except(['show']);

    // Tickets
    Route::resource('tickets', TicketEventController::class);

    // Jastip
    Route::resource('jastips', JastipTripController::class);
});
