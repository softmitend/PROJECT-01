<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberOrderController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StatusHistoryController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MemberTrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MemberTrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking', [MemberTrackingController::class, 'lookup'])->middleware('throttle:10,1')->name('tracking.lookup');
Route::get('/tracking/{memberCode}', [MemberTrackingController::class, 'member'])->name('tracking.member');
Route::get('/tracking/{memberCode}/orders/{memberOrder}', [MemberTrackingController::class, 'order'])->name('tracking.order');

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
        Route::resource('members', MemberController::class);
        Route::resource('batches', BatchController::class);
        Route::post('batches/{batch}/members', [BatchController::class, 'attachMember'])->name('batches.members.store');
        Route::post('batches/{batch}/status', [BatchController::class, 'transition'])->name('batches.status');
        Route::resource('products', ProductController::class)->except('show');
        Route::resource('order-statuses', OrderStatusController::class);
        Route::resource('member-orders', MemberOrderController::class);
        Route::get('status-histories', StatusHistoryController::class)->name('status-histories.index');
    });
