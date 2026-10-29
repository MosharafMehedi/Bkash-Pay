<?php

use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
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
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe');

/*
|--------------------------------------------------------------------------
| AUTH-REQUIRED ROUTES (with dynamic_permission middleware)
|--------------------------------------------------------------------------
| All routes below pass through DynamicPermissionMiddleware
| which derives permission from route name (convention-based).
*/

Route::middleware(['auth', 'dynamic_permission'])->group(function () {

    // ═══════ DASHBOARD ═══════
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ═══════ PRODUCTS (customer browse) ═══════
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

    // ═══════ PROFILE ═══════
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

    // ═══════ CART ═══════
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::get('/count', [CartController::class, 'count'])->name('count');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/', [CartController::class, 'clear'])->name('clear');
    });

    // ═══════ CHECKOUT ═══════
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::get('/checkout/delivery-charge', [CheckoutController::class, 'deliveryCharge'])
        ->name('checkout.deliveryCharge');
    Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');

    // ═══════ MY ORDERS (customer) ═══════
    Route::prefix('my-orders')->name('my-orders.')->group(function () {
        Route::get('/', [MyOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [MyOrderController::class, 'show'])->name('show');
    });

    // ═══════ REVIEWS (customer) ═══════
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');
    Route::patch('/reviews/{review}', [ReviewController::class, 'update'])
        ->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
        ->name('reviews.destroy');

    Route::post('/reviews/{review}/replies', [ReviewReplyController::class, 'store'])
        ->name('replies.store');
    Route::patch('/replies/{reply}', [ReviewReplyController::class, 'update'])
        ->name('replies.update');
    Route::delete('/replies/{reply}', [ReviewReplyController::class, 'destroy'])
        ->name('replies.destroy');

    /*
    |--------------------------------------------------------------------------
    | ADMIN PANEL
    |--------------------------------------------------------------------------
    | role:admin middleware bypass — but we still apply dynamic_permission
    | (which passes for admin via Gate::before bypass).
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // ═══ PRODUCTS ═══
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [AdminProductController::class, 'index'])->name('index');
            Route::get('/create', [AdminProductController::class, 'create'])->name('create');
            Route::post('/', [AdminProductController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [AdminProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('destroy');
            Route::patch('/{product}/toggle', [AdminProductController::class, 'toggleStatus'])->name('toggle');
        });

        // ═══ CATEGORIES ═══
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
            Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
            Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [AdminCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
            Route::patch('/{category}/toggle', [AdminCategoryController::class, 'toggle'])->name('toggle');
            Route::patch('/{category}/toggle-featured', [AdminCategoryController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::delete('/{category}/image', [AdminCategoryController::class, 'removeImage'])->name('remove-image');
        });

        // ═══ SLIDERS ═══
        Route::prefix('sliders')->name('sliders.')->group(function () {
            Route::get('/', [AdminSliderController::class, 'index'])->name('index');
            Route::get('/create', [AdminSliderController::class, 'create'])->name('create');
            Route::post('/', [AdminSliderController::class, 'store'])->name('store');
            Route::get('/{slider}/edit', [AdminSliderController::class, 'edit'])->name('edit');
            Route::put('/{slider}', [AdminSliderController::class, 'update'])->name('update');
            Route::delete('/{slider}', [AdminSliderController::class, 'destroy'])->name('destroy');
            Route::patch('/{slider}/toggle', [AdminSliderController::class, 'toggle'])->name('toggle');
            Route::delete('/{slider}/image', [AdminSliderController::class, 'removeImage'])->name('remove-image');
        });

        // ═══ ANNOUNCEMENTS ═══
        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/', [AdminAnnouncementController::class, 'index'])->name('index');
            Route::get('/create', [AdminAnnouncementController::class, 'create'])->name('create');
            Route::post('/', [AdminAnnouncementController::class, 'store'])->name('store');
            Route::get('/{announcement}/edit', [AdminAnnouncementController::class, 'edit'])->name('edit');
            Route::put('/{announcement}', [AdminAnnouncementController::class, 'update'])->name('update');
            Route::delete('/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('destroy');
            Route::patch('/{announcement}/toggle', [AdminAnnouncementController::class, 'toggle'])->name('toggle');
        });

        // ═══ HOMEPAGE SETTINGS ═══
        Route::prefix('homepage')->name('homepage.')->group(function () {
            Route::get('/', [AdminHomepageController::class, 'index'])->name('index');
            Route::put('/', [AdminHomepageController::class, 'update'])->name('update');
            Route::post('/reset', [AdminHomepageController::class, 'reset'])->name('reset');
        });

        // ═══ NEWSLETTER ═══
        Route::prefix('newsletter')->name('newsletter.')->group(function () {
            Route::get('/', [AdminNewsletterController::class, 'index'])->name('index');
            Route::get('/export', [AdminNewsletterController::class, 'export'])->name('export');
            Route::delete('/{subscriber}', [AdminNewsletterController::class, 'destroy'])->name('destroy');
        });

        // ═══ COUPONS ═══
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [AdminCouponController::class, 'index'])->name('index');
            Route::get('/create', [AdminCouponController::class, 'create'])->name('create');
            Route::post('/', [AdminCouponController::class, 'store'])->name('store');
            Route::get('/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('edit');
            Route::put('/{coupon}', [AdminCouponController::class, 'update'])->name('update');
            Route::delete('/{coupon}', [AdminCouponController::class, 'destroy'])->name('destroy');
            Route::patch('/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('toggle');
        });

        // ═══ REVIEWS ═══
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [AdminReviewController::class, 'index'])->name('index');
            Route::get('/{product}', [AdminReviewController::class, 'show'])->name('show');
            Route::delete('/{review}', [AdminReviewController::class, 'destroy'])->name('destroy');
            Route::patch('/{review}/approve', [AdminReviewController::class, 'approve'])->name('approve');
            Route::patch('/{review}/reject', [AdminReviewController::class, 'reject'])->name('reject');
            Route::delete('/replies/{reply}', [AdminReviewController::class, 'destroyReply'])->name('replies.destroy');
        });

        // ═══ ORDERS ═══
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::patch('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('status');
            Route::patch('/{order}/assign', [AdminOrderController::class, 'assign'])->name('assign');
            Route::post('/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('cancel');
            Route::post('/{order}/return', [AdminOrderController::class, 'return'])->name('return');
        });

        // ═══ DELIVERY CHARGES ═══
        Route::prefix('delivery-charges')->name('delivery-charges.')->group(function () {
            Route::get('/', [AdminDeliveryChargeController::class, 'index'])->name('index');
            Route::get('/create', [AdminDeliveryChargeController::class, 'create'])->name('create');
            Route::post('/', [AdminDeliveryChargeController::class, 'store'])->name('store');
            Route::get('/{deliveryCharge}/edit', [AdminDeliveryChargeController::class, 'edit'])->name('edit');
            Route::put('/{deliveryCharge}', [AdminDeliveryChargeController::class, 'update'])->name('update');
            Route::delete('/{deliveryCharge}', [AdminDeliveryChargeController::class, 'destroy'])->name('destroy');
            Route::patch('/{deliveryCharge}/toggle', [AdminDeliveryChargeController::class, 'toggle'])->name('toggle');
        });

        // ═══ USERS ═══
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/create', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle', [AdminUserController::class, 'toggle'])->name('toggle');
        });

        // ═══ ROLES ═══
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [AdminRoleController::class, 'index'])->name('index');
            Route::get('/create', [AdminRoleController::class, 'create'])->name('create');
            Route::post('/', [AdminRoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [AdminRoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [AdminRoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [AdminRoleController::class, 'destroy'])->name('destroy');
        });

        // ═══ PERMISSIONS ═══
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [AdminPermissionController::class, 'index'])->name('index');
            Route::get('/create', [AdminPermissionController::class, 'create'])->name('create');
            Route::post('/', [AdminPermissionController::class, 'store'])->name('store');
            Route::get('/{permission}/edit', [AdminPermissionController::class, 'edit'])->name('edit');
            Route::put('/{permission}', [AdminPermissionController::class, 'update'])->name('update');
            Route::delete('/{permission}', [AdminPermissionController::class, 'destroy'])->name('destroy');
        });

        // ═══ AUDIT LOGS ═══
        Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
            Route::get('/', [AdminAuditLogController::class, 'index'])->name('index');
            Route::get('/{auditLog}', [AdminAuditLogController::class, 'show'])->name('show');
            Route::delete('/{auditLog}', [AdminAuditLogController::class, 'destroy'])->name('destroy');
            Route::post('/purge', [AdminAuditLogController::class, 'purge'])->name('purge');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | DELIVERY PANEL (delivery_man, vendor, admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:delivery_man|vendor|admin'])
        ->prefix('delivery')
        ->name('delivery.')
        ->group(function () {
            Route::get('/dashboard', [DeliveryDashboardController::class, 'index'])->name('dashboard');
            Route::get('/orders', [DeliveryOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [DeliveryOrderController::class, 'show'])->name('orders.show');
            Route::post('/orders/{order}/out-for-delivery', [DeliveryOrderController::class, 'markOutForDelivery'])->name('orders.out');
            Route::post('/orders/{order}/verify-code', [DeliveryOrderController::class, 'verifyCode'])->name('orders.verify');
        });

    /*
    |--------------------------------------------------------------------------
    | PAYMENT GATEWAYS (initiation — auth)
    |--------------------------------------------------------------------------
    */
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
});

/*
|--------------------------------------------------------------------------
| GATEWAY CALLBACKS (NO auth — called by gateway)
|--------------------------------------------------------------------------
*/
Route::get('/bkash/callback', [BkashController::class, 'callback'])->name('bkash.callback');
Route::get('/paypal/callback', [PayPalController::class, 'callback'])->name('paypal.callback');
Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

Route::post('/sslcommerz/success', [SslCommerzController::class, 'success'])->name('sslcommerz.success');
Route::post('/sslcommerz/fail', [SslCommerzController::class, 'fail'])->name('sslcommerz.fail');
Route::post('/sslcommerz/cancel', [SslCommerzController::class, 'cancel'])->name('sslcommerz.cancel');
Route::post('/sslcommerz/ipn', [SslCommerzController::class, 'ipn'])->name('sslcommerz.ipn');

require __DIR__ . '/auth.php';