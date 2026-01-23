<?php

namespace App\Actions;

use App\Jobs\Backup\CreateBackupJob;
use App\Models\Vault;

class BackupActions
{
    public function uploadBackup(string $storedPath, Vault $vault, array $meta): void
    {
        if (app()->environment('local')) {
            CreateBackupJob::dispatchSync($storedPath, $vault, $meta);

            return;
        }

        CreateBackupJob::dispatch($storedPath, $vault, $meta);
    }

    public function getBackupContents() {}
}
