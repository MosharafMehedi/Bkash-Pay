<?php

use App\Http\Controllers\BkashController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SslCommerzController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    // ── User (all authenticated roles) ──
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/checkout/{product}', [ProductController::class, 'checkout'])->name('checkout.show');

    // ── Coupon apply ──
    Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');

    // ═══════════════════════════════════════════════════════════
    //  ADMIN PANEL — only role:admin
    // ═══════════════════════════════════════════════════════════
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Products
            Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
            Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
            Route::patch('/products/{product}/toggle', [AdminProductController::class, 'toggleStatus'])->name('products.toggle');

            // Coupons
            Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
            Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
            Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
            Route::get('/coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
            Route::put('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
            Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
            Route::patch('/coupons/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');

            // Users (permission: user.view)
            Route::middleware('permission:user.view')->group(function () {
                Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
                Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
                Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
                Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
                Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
                Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
                Route::patch('/users/{user}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
            });

            // Roles (permission: role.view)
            Route::middleware('permission:role.view')->group(function () {
                Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
                Route::get('/roles/create', [AdminRoleController::class, 'create'])->name('roles.create');
                Route::post('/roles', [AdminRoleController::class, 'store'])->name('roles.store');
                Route::get('/roles/{role}/edit', [AdminRoleController::class, 'edit'])->name('roles.edit');
                Route::put('/roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
                Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');
            });

            // Permissions (permission: permission.view)
            Route::middleware('permission:permission.view')->group(function () {
                Route::get('/permissions', [AdminPermissionController::class, 'index'])->name('permissions.index');
            });
        });

    // ═══════════════════════════════════════════════════════════
    //  DELIVERY / VENDOR PANEL — placeholder (future delivery)
    // ═══════════════════════════════════════════════════════════
    Route::middleware('role:delivery_man|vendor|admin')
        ->prefix('delivery')
        ->name('delivery.')
        ->group(function () {
            Route::get('/dashboard', function () {
                return 'Delivery dashboard — coming soon';
            })->name('dashboard');
        });

    // ── bKash ──
    Route::post('/bkash/pay', [BkashController::class, 'pay'])->name('bkash.pay');
    Route::get('/bkash/history', [BkashController::class, 'index'])->name('bkash.index');
    Route::get('/bkash/status/{paymentId}', [BkashController::class, 'status'])->name('bkash.status');

    // ── PayPal ──
    Route::post('/paypal/pay', [PayPalController::class, 'pay'])->name('paypal.pay');
    Route::get('/paypal/history', [PayPalController::class, 'index'])->name('paypal.index');

    // ── SSLCommerz ──
    Route::post('/sslcommerz/pay', [SslCommerzController::class, 'pay'])->name('sslcommerz.pay');
    Route::get('/sslcommerz/history', [SslCommerzController::class, 'index'])->name('sslcommerz.index');

    // ── Cash on Delivery (multi-step OTP flow) ──
    Route::prefix('cash')->name('cash.')->group(function () {
        Route::post('/pay', [CashController::class, 'pay'])->name('pay');
        Route::get('/{order}/confirm', [CashController::class, 'confirm'])->name('confirm');
        Route::post('/{order}/send-otp', [CashController::class, 'sendOtp'])->name('sendOtp');
        Route::get('/{order}/verify', [CashController::class, 'verify'])->name('verify');
        Route::post('/{order}/submit-otp', [CashController::class, 'submitOtp'])->name('submitOtp');
        Route::post('/{order}/resend-otp', [CashController::class, 'resendOtp'])->name('resendOtp');
        Route::post('/{order}/cancel', [CashController::class, 'cancel'])->name('cancel');
    });

    // ── Profile ──
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Gateway callbacks (outside auth) ──
Route::get('/bkash/callback', [BkashController::class, 'callback'])->name('bkash.callback');
Route::get('/paypal/callback', [PayPalController::class, 'callback'])->name('paypal.callback');
Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

Route::post('/sslcommerz/success', [SslCommerzController::class, 'success'])->name('sslcommerz.success');
Route::post('/sslcommerz/fail', [SslCommerzController::class, 'fail'])->name('sslcommerz.fail');
Route::post('/sslcommerz/cancel', [SslCommerzController::class, 'cancel'])->name('sslcommerz.cancel');
Route::post('/sslcommerz/ipn', [SslCommerzController::class, 'ipn'])->name('sslcommerz.ipn');

require __DIR__.'/auth.php';