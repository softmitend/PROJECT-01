<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberOrderController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\StatusHistoryController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LineAuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\MemberTrackingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MemberTrackingController::class, 'home'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/billing', [CustomerPortalController::class, 'billing'])->name('billing.index');
Route::get('/billing/orders', [CustomerPortalController::class, 'billingOrders'])->name('billing.orders');
Route::get('/billing/ems', [CustomerPortalController::class, 'billingEms'])->name('billing.ems');
Route::get('/services', [CustomerPortalController::class, 'services'])->name('services.index');
Route::get('/order-history', [CustomerPortalController::class, 'history'])->name('orders.history');
Route::get('/tracking', [MemberTrackingController::class, 'index'])->name('tracking.index');
Route::get('/catalog/{batch}', [CatalogController::class, 'show'])->name('catalog.show');
Route::post('/catalog/{batch}/checkout', [CatalogController::class, 'store'])->middleware('throttle:5,1')->name('catalog.checkout');
Route::post('/search', [MemberTrackingController::class, 'smartLookup'])->middleware('throttle:10,1')->name('tracking.search');
Route::post('/tracking', [MemberTrackingController::class, 'lookup'])->middleware('throttle:10,1')->name('tracking.lookup');
Route::get('/tracking/order/{orderCode}', [MemberTrackingController::class, 'progress'])->middleware(['signed', 'throttle:30,1'])->name('tracking.progress');
Route::post('/history', [MemberTrackingController::class, 'historyLookup'])->middleware('throttle:10,1')->name('tracking.history.lookup');
Route::get('/history/{memberCode}', [MemberTrackingController::class, 'member'])->middleware(['signed', 'throttle:30,1'])->name('tracking.member');
Route::get('/history/{memberCode}/orders/{memberOrder}', [MemberTrackingController::class, 'order'])->middleware(['signed', 'throttle:30,1'])->name('tracking.order');
Route::get('/profile', ProfileController::class)->name('profile.show');
Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
Route::get('/testimonials/{orderItem}/create', [TestimonialController::class, 'create'])->name('testimonials.create');
Route::post('/testimonials/{orderItem}', [TestimonialController::class, 'store'])->middleware('throttle:5,1')->name('testimonials.store');
Route::get('/profile/orders/{memberOrder}/payment-proof', [ProfileController::class, 'paymentProof'])
    ->middleware('auth')
    ->name('profile.orders.payment-proof');
Route::get('/auth/line', [LineAuthController::class, 'redirect'])->middleware('throttle:10,1')->name('line-auth.redirect');
Route::get('/auth/line/callback', [LineAuthController::class, 'callback'])->middleware('throttle:10,1')->name('line-auth.callback');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'can:access-admin'])
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('batches', BatchController::class);
        Route::post('batches/{batch}/status', [BatchController::class, 'transition'])->name('batches.status');
        Route::resource('order-statuses', OrderStatusController::class);
        Route::resource('member-orders', MemberOrderController::class);
        Route::get('member-orders/{member_order}/payment-proof', [MemberOrderController::class, 'paymentProof'])->name('member-orders.payment-proof');
        Route::post('member-orders/{member_order}/status', [MemberOrderController::class, 'transition'])->name('member-orders.status');
        Route::get('status-histories', StatusHistoryController::class)->name('status-histories.index');
    });
