<?php

use App\Http\Controllers\FrontController;
use App\Http\Controllers\FrontOrderController;
use App\Http\Controllers\Management\AuthController;
use App\Http\Controllers\Management\CategoriesController;
use App\Http\Controllers\Management\DashboardController;
use App\Http\Controllers\Management\OrdersController;
use App\Http\Controllers\Management\ProductsController;
use App\Http\Controllers\Management\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontController::class, 'home']);
Route::get('/product/{product}', [FrontController::class, 'product'])->name('product.show');
Route::post('/order', [FrontOrderController::class, 'store'])->name('order.store');

Route::prefix('management')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('management.login');
    Route::post('/login', [AuthController::class, 'login'])->name('management.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('management.logout');

    Route::middleware(['auth', 'active'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('management.dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/orders', [OrdersController::class, 'index'])->name('management.orders.index');
        Route::get('/orders/archive', [OrdersController::class, 'archive'])->name('management.orders.archive');
        Route::post('/orders/{order}/status', [OrdersController::class, 'updateStatus'])->name('management.orders.status');
        Route::get('/password', [AuthController::class, 'showChangePassword'])->name('management.password');
        Route::post('/password', [AuthController::class, 'updatePassword'])->name('management.password.update');

        Route::middleware(['admin'])->group(function () {
            Route::get('/products', [ProductsController::class, 'index'])->name('management.products.index');
            Route::get('/products/create', [ProductsController::class, 'create'])->name('management.products.create');
            Route::post('/products', [ProductsController::class, 'store'])->name('management.products.store');
            Route::get('/products/{product}/edit', [ProductsController::class, 'edit'])->name('management.products.edit');
            Route::put('/products/{product}', [ProductsController::class, 'update'])->name('management.products.update');
            Route::delete('/products/{product}', [ProductsController::class, 'destroy'])->name('management.products.destroy');
            Route::get('/categories', [CategoriesController::class, 'index'])->name('management.categories.index');
            Route::post('/categories', [CategoriesController::class, 'store'])->name('management.categories.store');
            Route::put('/categories/{category}', [CategoriesController::class, 'update'])->name('management.categories.update');
            Route::get('/users', [UsersController::class, 'index'])->name('management.users.index');
            Route::post('/users', [UsersController::class, 'store'])->name('management.users.store');
            Route::post('/users/{user}/toggle', [UsersController::class, 'toggle'])->name('management.users.toggle');
        });
    });
});
