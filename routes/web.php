<?php

use App\Http\Controllers\BkashController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\SslCommerzController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bkash', [BkashController::class, 'index'])->name('bkash.index');
Route::post('/bkash/pay', [BkashController::class, 'pay'])->name('bkash.pay');
Route::get('/bkash/callback', [BkashController::class, 'callback'])->name('bkash.callback');
Route::get('/bkash/status/{paymentId}', [BkashController::class, 'status'])->name('bkash.status');

// PayPal
Route::get('/paypal', [PayPalController::class, 'index'])->name('paypal.index');
Route::post('/paypal/pay', [PayPalController::class, 'pay'])->name('paypal.pay');
Route::get('/paypal/callback', [PayPalController::class, 'callback'])->name('paypal.callback');
Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

// SSLCommerz
Route::get('/sslcommerz', [SslCommerzController::class, 'index'])->name('sslcommerz.index');
Route::post('/sslcommerz/pay', [SslCommerzController::class, 'pay'])->name('sslcommerz.pay');
Route::post('/sslcommerz/success', [SslCommerzController::class, 'success'])->name('sslcommerz.success');
Route::post('/sslcommerz/fail', [SslCommerzController::class, 'fail'])->name('sslcommerz.fail');
Route::post('/sslcommerz/cancel', [SslCommerzController::class, 'cancel'])->name('sslcommerz.cancel');
Route::post('/sslcommerz/ipn', [SslCommerzController::class, 'ipn'])->name('sslcommerz.ipn');
