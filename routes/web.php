<?php

use App\Http\Controllers\Public\OrderModalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProductController as PublicProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;


Route::post('/order-modal', [OrderModalController::class, 'store'])->name('order.modal.store');

// Push Notifications API
Route::post('/api/push-subscribe', [PushSubscriptionController::class, 'subscribe'])->middleware('api');
Route::post('/api/push-unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->middleware('api');
Route::get('/api/push/public-key', [PushSubscriptionController::class, 'getPublicKey']);
Route::post('/api/push/subscribe', [PushSubscriptionController::class, 'subscribe'])->middleware('auth');
Route::post('/api/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->middleware('auth');
Route::get('/api/push/status', [PushSubscriptionController::class, 'status'])->middleware('auth');

// Page de test push notifications
Route::get('/push-test', function () {
    return view('push-test');
})->middleware('auth');

// URL de test sans Service Worker (info uniquement)
Route::get('/push-info', function () {
    return view('push-info');
})->middleware('auth');

Route::get('/', [HomeController::class, 'index']);
Route::get('/products/{product}', [PublicProductController::class, 'show'])->name('products.show');

// Dashboard - Admin only
Route::middleware(['auth', 'active', 'role:admin'])->group(function () {
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin & Manager - Orders management
Route::middleware(['auth', 'active', 'role:admin,manager'])->group(function () {
	Route::prefix('admin')
		->name('admin.')
		->group(function () {
			Route::resource('orders', OrderController::class)->only(['index', 'update']);
		});
});

Route::prefix('admin')
	->name('admin.')
	->middleware(['auth', 'active', 'role:admin'])
	->group(function () {
		Route::resource('products', ProductController::class)->except(['show']);
		Route::resource('users', UserController::class)->except(['show']);
	});

require __DIR__.'/auth.php';
