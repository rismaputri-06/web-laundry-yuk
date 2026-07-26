<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'signup'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()->isAdmin()
            ? redirect('/admin/dashboard')
            : redirect('/customer/dashboard');
    });

    Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');

    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
        Route::post('/admin/orders', [AdminController::class, 'storeOrder'])->name('admin.orders.store');
        Route::get('/admin/orders/{order}', [AdminController::class, 'orderDetail'])->name('admin.orders.show');
        Route::put('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');

        Route::get('/admin/services', [AdminController::class, 'services'])->name('admin.services');

        Route::get('/admin/customers', [AdminController::class, 'customers'])->name('admin.customers');
        Route::get('/admin/customers/{customer}/detail', [AdminController::class, 'customerDetail'])->name('admin.customers.detail');

        Route::get('/admin/pickup-delivery', [AdminController::class, 'pickupDeliveries'])->name('admin.pickup-delivery');
        Route::put('/admin/pickup-delivery/{pickupDelivery}/status', [AdminController::class, 'updatePickupStatus'])->name('admin.pickup-delivery.status');
    });
    Route::get('/api/orders', [OrderController::class, 'index']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/customer/orders/create', [CustomerController::class, 'newOrder'])->name('customer.orders.create');
    Route::post('/api/orders', [OrderController::class, 'store']);
    Route::get('/customer/orders/{order}', [CustomerController::class, 'orderDetail'])->name('customer.orders.show');

    Route::get('/api/profile', [ProfileController::class, 'show']);
    Route::put('/api/profile', [ProfileController::class, 'update']);
    Route::put('/api/profile/password', [ProfileController::class, 'updatePassword']);

});

