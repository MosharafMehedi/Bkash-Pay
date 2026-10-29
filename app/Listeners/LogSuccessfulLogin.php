<?php

namespace App\Listeners;

use App\Services\AuditLogService;
use Illuminate\Auth\Events\Login;
use Log;

class LogSuccessfulLogin
{
    public function __construct(protected AuditLogService $audit) {}

    public function handle(Login $event): void
    {
        try {
            $this->audit->logLogin($event->user);
        } catch (\Throwable $e) {
            Log::error('Login audit failed: ' . $e->getMessage());
        }
    }
}