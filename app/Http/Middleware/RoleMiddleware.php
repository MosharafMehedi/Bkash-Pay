<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage: ->middleware('role:admin')
     *        ->middleware('role:admin|manager')
     *        ->middleware('role:admin,manager')  // alt
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            abort(403, 'Unauthenticated.');
        }

        // Support both `role:admin|manager` (single string) and `role:admin,manager` (spread)
        $roles = array_merge(
            $roles,
            ...array_map(fn ($r) => explode('|', $r), $roles)
        );

        $roles = array_unique(array_filter($roles));

        foreach ($roles as $role) {
            if ($request->user()->hasRole(trim($role))) {
                return $next($request);
            }
        }

        abort(403, 'You do not have the required role.');
    }
}