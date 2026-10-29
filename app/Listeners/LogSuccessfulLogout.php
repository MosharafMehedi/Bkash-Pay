<?php

namespace App\Listeners;

use App\Services\AuditLogService;
use Illuminate\Auth\Events\Logout;
use Log;

class LogSuccessfulLogout
{
    public function __construct(protected AuditLogService $audit) {}

    public function handle(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        try {
            $this->audit->logLogout($event->user);
        } catch (\Throwable $e) {
            Log::error('Logout audit failed: ' . $e->getMessage());
        }
    }
}