<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ═══ Admin bypasses all permission checks ═══
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        // ═══ Blade component alias ═══
        Blade::component('welcome-layout', \App\View\Components\WelcomeLayout::class);
    }
}