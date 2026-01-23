<?php

namespace App\Actions;

use App\Jobs\Backup\CreateBackupJob;
use App\Models\Vault;

class BackupActions
{
    public function uploadBackup(string $filePath, Vault $vault): void
    {
        CreateBackupJob::dispatch();
    }

    public function getBackupContents() {}
}
