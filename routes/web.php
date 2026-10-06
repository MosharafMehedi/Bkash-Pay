<?php

use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DeliveryChargeController as AdminDeliveryChargeController;
use App\Http\Controllers\Admin\HomepageController as AdminHomepageController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BkashController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Delivery\DashboardController as DeliveryDashboardController;
use App\Http\Controllers\Delivery\OrderController as DeliveryOrderController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewReplyController;
use App\Http\Controllers\SslCommerzController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');

Route::get('/newsletter/unsubscribe/{token}', [\App\Http\Controllers\NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe');

Route::middleware('auth')->group(function () {

    // ═══════════════════════════════════════════════════════════
    //  USER ROUTES
    // ═══════════════════════════════════════════════════════════

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

    // ═══ Reviews ═══
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::post('/reviews/{review}/replies', [ReviewReplyController::class, 'store'])->name('replies.store');
    Route::patch('/replies/{reply}', [ReviewReplyController::class, 'update'])->name('replies.update');
    Route::delete('/replies/{reply}', [ReviewReplyController::class, 'destroy'])->name('replies.destroy');

    // ═══ Checkout ═══
    Route::get('/checkout/delivery-charge', [CheckoutController::class, 'deliveryCharge'])
        ->name('checkout.deliveryCharge');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');

    // ═══ Coupon ═══
    Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');

    // ═══ My Orders (customer) ═══
    Route::get('/my-orders', [MyOrderController::class, 'index'])->name('my-orders.index');
    Route::get('/my-orders/{order}', [MyOrderController::class, 'show'])->name('my-orders.show');

    // ═══ Cart ═══
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/', [CartController::class, 'clear'])->name('clear');
        Route::get('/count', [CartController::class, 'count'])->name('count');
    });

    // ═══════════════════════════════════════════════════════════
    //  ADMIN PANEL — only role:admin
    // ═══════════════════════════════════════════════════════════
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // ═══ Products ═══
            Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
            Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
            Route::patch('/products/{product}/toggle', [AdminProductController::class, 'toggleStatus'])->name('products.toggle');

            // ═══ Categories ═══
            Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
            Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
            Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
            Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
            Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
            Route::patch('/categories/{category}/toggle', [AdminCategoryController::class, 'toggle'])->name('categories.toggle');
            Route::patch('/categories/{category}/toggle-featured', [AdminCategoryController::class, 'toggleFeatured'])->name('categories.toggle-featured');
            Route::delete('/categories/{category}/image', [AdminCategoryController::class, 'removeImage'])->name('categories.remove-image');

            Route::get('/sliders', [AdminSliderController::class, 'index'])->name('sliders.index');
            Route::get('/sliders/create', [AdminSliderController::class, 'create'])->name('sliders.create');
            Route::post('/sliders', [AdminSliderController::class, 'store'])->name('sliders.store');
            Route::get('/sliders/{slider}/edit', [AdminSliderController::class, 'edit'])->name('sliders.edit');
            Route::put('/sliders/{slider}', [AdminSliderController::class, 'update'])->name('sliders.update');
            Route::delete('/sliders/{slider}', [AdminSliderController::class, 'destroy'])->name('sliders.destroy');
            Route::patch('/sliders/{slider}/toggle', [AdminSliderController::class, 'toggle'])->name('sliders.toggle');
            Route::delete('/sliders/{slider}/image', [AdminSliderController::class, 'removeImage'])->name('sliders.remove-image');

            Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->name('announcements.index');
            Route::get('/announcements/create', [AdminAnnouncementController::class, 'create'])->name('announcements.create');
            Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->name('announcements.store');
            Route::get('/announcements/{announcement}/edit', [AdminAnnouncementController::class, 'edit'])->name('announcements.edit');
            Route::put('/announcements/{announcement}', [AdminAnnouncementController::class, 'update'])->name('announcements.update');
            Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');
            Route::patch('/announcements/{announcement}/toggle', [AdminAnnouncementController::class, 'toggle'])->name('announcements.toggle');

            // ═══ Homepage Settings ═══
            Route::get('/homepage', [AdminHomepageController::class, 'index'])->name('homepage.index');
            Route::put('/homepage', [AdminHomepageController::class, 'update'])->name('homepage.update');
            Route::post('/homepage/reset', [AdminHomepageController::class, 'reset'])->name('homepage.reset');

            // ═══ Newsletter ═══
            Route::get('/newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
            Route::get('/newsletter/export', [AdminNewsletterController::class, 'export'])->name('newsletter.export');
            Route::delete('/newsletter/{subscriber}', [AdminNewsletterController::class, 'destroy'])->name('newsletter.destroy');
            // ═══ Coupons ═══
            Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
            Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
            Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
            Route::get('/coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
            Route::put('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
            Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
            Route::patch('/coupons/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');

            // ═══ Reviews ═══
            Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
            Route::get('/reviews/{product}', [AdminReviewController::class, 'show'])->name('reviews.show');
            Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
            Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
            Route::patch('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
            Route::delete('/replies/{reply}', [AdminReviewController::class, 'destroyReply'])->name('replies.destroy');

            // ═══ Orders ═══
            Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
            Route::patch('/orders/{order}/assign', [AdminOrderController::class, 'assign'])->name('orders.assign');
            Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
            Route::post('/orders/{order}/return', [AdminOrderController::class, 'return'])->name('orders.return');

            // ═══ Delivery Charges ═══
            Route::get('/delivery-charges', [AdminDeliveryChargeController::class, 'index'])->name('delivery-charges.index');
            Route::get('/delivery-charges/create', [AdminDeliveryChargeController::class, 'create'])->name('delivery-charges.create');
            Route::post('/delivery-charges', [AdminDeliveryChargeController::class, 'store'])->name('delivery-charges.store');
            Route::get('/delivery-charges/{deliveryCharge}/edit', [AdminDeliveryChargeController::class, 'edit'])->name('delivery-charges.edit');
            Route::put('/delivery-charges/{deliveryCharge}', [AdminDeliveryChargeController::class, 'update'])->name('delivery-charges.update');
            Route::delete('/delivery-charges/{deliveryCharge}', [AdminDeliveryChargeController::class, 'destroy'])->name('delivery-charges.destroy');
            Route::patch('/delivery-charges/{deliveryCharge}/toggle', [AdminDeliveryChargeController::class, 'toggle'])->name('delivery-charges.toggle');

            // ═══ Users ═══
            Route::middleware('permission:user.view')->group(function () {
                Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
                Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
                Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
                Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
                Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
                Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
                Route::patch('/users/{user}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
            });

            // ═══ Roles ═══
            Route::middleware('permission:role.view')->group(function () {
                Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
                Route::get('/roles/create', [AdminRoleController::class, 'create'])->name('roles.create');
                Route::post('/roles', [AdminRoleController::class, 'store'])->name('roles.store');
                Route::get('/roles/{role}/edit', [AdminRoleController::class, 'edit'])->name('roles.edit');
                Route::put('/roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
                Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');
            });

            // ═══ Permissions ═══
            Route::middleware('permission:permission.view')->group(function () {
                Route::get('/permissions', [AdminPermissionController::class, 'index'])->name('permissions.index');
            });
        });

    // ═══════════════════════════════════════════════════════════
    //  DELIVERY PANEL — delivery_man / vendor / admin
    // ═══════════════════════════════════════════════════════════
    Route::middleware('role:delivery_man|vendor|admin')
        ->prefix('delivery')
        ->name('delivery.')
        ->group(function () {
            Route::get('/dashboard', [DeliveryDashboardController::class, 'index'])->name('dashboard');
            Route::get('/orders', [DeliveryOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [DeliveryOrderController::class, 'show'])->name('orders.show');
            Route::post('/orders/{order}/verify-code', [DeliveryOrderController::class, 'verifyCode'])->name('orders.verify');
            Route::post('/orders/{order}/out-for-delivery', [DeliveryOrderController::class, 'markOutForDelivery'])->name('orders.out');
        });

    // ═══ Payment Gateways ═══

    // bKash
    Route::post('/bkash/pay', [BkashController::class, 'pay'])->name('bkash.pay');
    Route::get('/bkash/history', [BkashController::class, 'index'])->name('bkash.index');
    Route::get('/bkash/status/{paymentId}', [BkashController::class, 'status'])->name('bkash.status');

    // PayPal
    Route::post('/paypal/pay', [PayPalController::class, 'pay'])->name('paypal.pay');
    Route::get('/paypal/history', [PayPalController::class, 'index'])->name('paypal.index');

    // SSLCommerz
    Route::post('/sslcommerz/pay', [SslCommerzController::class, 'pay'])->name('sslcommerz.pay');
    Route::get('/sslcommerz/history', [SslCommerzController::class, 'index'])->name('sslcommerz.index');

    // Cash on Delivery
    Route::prefix('cash')->name('cash.')->group(function () {
        Route::post('/pay', [CashController::class, 'pay'])->name('pay');
        Route::get('/{order}/confirm', [CashController::class, 'confirm'])->name('confirm');
        Route::post('/{order}/send-otp', [CashController::class, 'sendOtp'])->name('sendOtp');
        Route::get('/{order}/verify', [CashController::class, 'verify'])->name('verify');
        Route::post('/{order}/submit-otp', [CashController::class, 'submitOtp'])->name('submitOtp');
        Route::post('/{order}/resend-otp', [CashController::class, 'resendOtp'])->name('resendOtp');
        Route::post('/{order}/cancel', [CashController::class, 'cancel'])->name('cancel');
    });

    // ═══ Profile ═══
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
});

// ═══════════════════════════════════════════════════════════
//  GATEWAY CALLBACKS (outside auth)
// ═══════════════════════════════════════════════════════════
Route::get('/bkash/callback', [BkashController::class, 'callback'])->name('bkash.callback');
Route::get('/paypal/callback', [PayPalController::class, 'callback'])->name('paypal.callback');
Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

Route::post('/sslcommerz/success', [SslCommerzController::class, 'success'])->name('sslcommerz.success');
Route::post('/sslcommerz/fail', [SslCommerzController::class, 'fail'])->name('sslcommerz.fail');
Route::post('/sslcommerz/cancel', [SslCommerzController::class, 'cancel'])->name('sslcommerz.cancel');
Route::post('/sslcommerz/ipn', [SslCommerzController::class, 'ipn'])->name('sslcommerz.ipn');

require __DIR__ . '/auth.php';
