<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BkashPaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bkash', [BkashPaymentController::class, 'index'])->name('bkash.index');
Route::post('/bkash/pay', [BkashPaymentController::class, 'pay'])->name('bkash.pay');
Route::get('/bkash/callback', [BkashPaymentController::class, 'callback'])->name('bkash.callback');
Route::get('/bkash/status/{paymentId}', [BkashPaymentController::class, 'status'])->name('bkash.status');