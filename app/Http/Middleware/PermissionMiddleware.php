<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Usage: ->middleware('permission:product.view')
     *        ->middleware('permission:product.view|product.edit')   // any
     *        ->middleware('permission:product.view,product.edit')   // any (spread)
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (! $request->user()) {
            abort(403, 'Unauthenticated.');
        }

        $permissions = array_merge(
            $permissions,
            ...array_map(fn ($p) => explode('|', $p), $permissions)
        );

        $permissions = array_unique(array_filter($permissions));

        foreach ($permissions as $permission) {
            if ($request->user()->can(trim($permission))) {
                return $next($request);
            }
        }

        abort(403, 'You do not have the required permission.');
    }
}