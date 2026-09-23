<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\PermissionRegistrar;

class ClearPermissionCache extends Command
{
    protected $signature   = 'permission:clear';
    protected $description = 'Clear Spatie permission cache';

    public function handle(): int
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info('Permission cache cleared.');
        return self::SUCCESS;
    }
}