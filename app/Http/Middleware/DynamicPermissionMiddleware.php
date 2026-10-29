<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DynamicPermissionMiddleware
{
    /**
     * Route name action → permission action map.
     * Custom action add korte chaile ekhane add korben.
     */
    protected array $actionMap = [
        // Standard CRUD
        'index'            => 'view',
        'show'             => 'view',
        'create'           => 'create',
        'store'            => 'create',
        'edit'             => 'edit',
        'update'           => 'edit',
        'destroy'          => 'delete',

        // Toggle / extras (admin UI actions)
        'toggle'           => 'edit',
        'toggle-featured'  => 'edit',
        'toggle-status'    => 'edit',
        'toggle-active'    => 'edit',
        'remove-image'     => 'edit',

        // Export / purge
        'export'           => 'export',
        'purge'            => 'delete',

        // Order specific
        'status'           => 'update-status',
        'assign'           => 'assign',
        'cancel'           => 'cancel',
        'return'           => 'return',

        // Delivery
        'verify'           => 'verify-code',
        'out'              => 'update-status',
        'verify-code'      => 'verify-code',
        'mark-out-for-delivery' => 'update-status',

        // Review
        'approve'          => 'approve',
        'reject'           => 'reject',
    ];

    /**
     * Modules that should be skipped (no permission check).
     * e.g. dashboard, profile — sob user access korte parbe।
     */
    protected array $skipModules = [
        'dashboard',
        'profile',
        'welcome',
        'home',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        // No route name → skip
        if (! $routeName) {
            return $next($request);
        }

        // Skip pattern check (public routes)
        if ($this->shouldSkip($routeName)) {
            return $next($request);
        }

        // Derive permission name from route name
        $permission = $this->derivePermission($routeName);

        // Can't derive (custom pattern) → skip
        if (! $permission) {
            return $next($request);
        }

        // Auth check
        if (! $request->user()) {
            abort(403, 'Unauthenticated.');
        }

        // Spatie permission check
        if (! $request->user()->can($permission)) {
            abort(403, "You do not have permission: {$permission}");
        }

        return $next($request);
    }

    /**
     * Skip check — public routes, callbacks, dashboard, etc.
     */
    protected function shouldSkip(string $routeName): bool
    {
        // Skip exact prefix matches
        $skipPrefixes = [
            'welcome', 'home',
            'newsletter.subscribe', 'newsletter.unsubscribe',
            'password.', 'verification.',
            'login', 'register', 'logout',
            'bkash.callback', 'paypal.callback', 'paypal.cancel',
            'sslcommerz.success', 'sslcommerz.fail', 'sslcommerz.cancel', 'sslcommerz.ipn',
        ];

        foreach ($skipPrefixes as $prefix) {
            if ($routeName === $prefix || str_starts_with($routeName, $prefix)) {
                return true;
            }
        }

        // Skip modules
        $parts = explode('.', $routeName);
        $module = $parts[1] ?? null;

        if ($module && in_array($module, $this->skipModules)) {
            return true;
        }

        // Payment initiation routes — permission has prefix `payment.xxx`
        // but route name `bkash.pay` / `paypal.pay` etc. — special handling
        if (in_array($routeName, [
            'bkash.pay', 'bkash.index', 'bkash.status',
            'paypal.pay', 'paypal.index',
            'sslcommerz.pay', 'sslcommerz.index',
        ])) {
            return true; // handled below in `derivePermission` for payments
        }

        return false;
    }

    /**
     * Derive permission from route name.
     *
     * Examples:
     *   admin.users.index      → user.view
     *   admin.products.store   → product.create
     *   admin.orders.status    → order.update-status
     *   delivery.orders.show   → delivery.view
     *   bkash.pay              → payment.bkash
     */
    protected function derivePermission(string $routeName): ?string
    {
        // ═══ Payment gateways ═══
        $paymentMap = [
            'bkash.pay'        => 'payment.bkash',
            'bkash.index'      => 'payment.bkash',
            'bkash.status'     => 'payment.bkash',
            'paypal.pay'       => 'payment.paypal',
            'paypal.index'     => 'payment.paypal',
            'sslcommerz.pay'   => 'payment.sslcommerz',
            'sslcommerz.index' => 'payment.sslcommerz',
            'cash.pay'         => 'payment.cash',
            'cash.confirm'     => 'payment.cash',
            'cash.sendOtp'     => 'payment.cash',
            'cash.verify'      => 'payment.cash',
            'cash.submitOtp'   => 'payment.cash',
            'cash.resendOtp'   => 'payment.cash',
            'cash.cancel'      => 'payment.cash',
        ];

        if (isset($paymentMap[$routeName])) {
            return $paymentMap[$routeName];
        }

        // ═══ Cart / Checkout / Coupon (customer) ═══
        $customerMap = [
            'cart.index'          => 'cart.view',
            'cart.count'          => 'cart.view',
            'cart.add'            => 'cart.manage',
            'cart.update'         => 'cart.manage',
            'cart.remove'         => 'cart.manage',
            'cart.clear'          => 'cart.manage',
            'checkout.show'       => 'checkout.view',
            'checkout.deliveryCharge' => 'checkout.view',
            'coupon.apply'        => 'checkout.apply-coupon',
            'my-orders.index'     => 'order.view-own',
            'my-orders.show'      => 'order.view-own',
        ];

        if (isset($customerMap[$routeName])) {
            return $customerMap[$routeName];
        }

        // ═══ Reviews (customer) ═══
        if (str_starts_with($routeName, 'reviews.')) {
            return 'product.view'; // reviewing a product requires product view
        }
        if (str_starts_with($routeName, 'replies.')) {
            return 'product.view';
        }

        // ═══ Delivery panel ═══
        if (str_starts_with($routeName, 'delivery.')) {
            $action = explode('.', $routeName)[2] ?? 'view';

            return match ($action) {
                'dashboard' => 'dashboard.view',
                'index', 'show' => 'delivery.view',
                'out', 'mark-out-for-delivery' => 'delivery.update-status',
                'verify', 'verify-code' => 'delivery.verify-code',
                default => 'delivery.view',
            };
        }

        // ═══ Admin routes ═══
        if (! str_starts_with($routeName, 'admin.')) {
            return null;
        }

        $parts = explode('.', $routeName);
        // admin.users.index → ['admin', 'users', 'index']

        $module = $parts[1] ?? null;
        $action = $parts[2] ?? null;

        if (! $module || ! $action) {
            return null;
        }

        // Plural → singular conversion
        $moduleSingular = $this->pluralToSingular($module);

        // Action → permission action map
        $permAction = $this->actionMap[$action] ?? $action;

        // Special: delivery-charges → delivery-charge
        if ($moduleSingular === 'delivery-charge') {
            return "delivery-charge.{$permAction}";
        }

        // Special: audit-logs → audit-log
        if ($moduleSingular === 'audit-log') {
            return "audit-log.{$permAction}";
        }

        return "{$moduleSingular}.{$permAction}";
    }

    /**
     * Plural → singular (simple English rules).
     * users → user
     * categories → category
     * products → product
     */
    protected function pluralToSingular(string $word): string
    {
        // Already singular custom cases
        if (in_array($word, ['delivery-charges'])) {
            return 'delivery-charge';
        }
        if (in_array($word, ['audit-logs'])) {
            return 'audit-log';
        }

        // ies → y  (categories → category)
        if (str_ends_with($word, 'ies')) {
            return substr($word, 0, -3) . 'y';
        }

        // ses, xes, ches, shes → remove "es"
        if (preg_match('/(ses|xes|ches|shes)$/', $word)) {
            return substr($word, 0, -2);
        }

        // General s → remove s
        if (str_ends_with($word, 's')) {
            return substr($word, 0, -1);
        }

        return $word;
    }
}