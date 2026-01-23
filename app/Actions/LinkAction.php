<?php

namespace App\Actions;

use App\Models\Backup;
use Illuminate\Support\Facades\URL;

// The link will have access to anything which belongs to the model for example vault -> folders -> backups
class LinkAction
{
    public function createLinkForBackup(Backup $backup)
    {
        return URL::temporarySignedRoute(
            route('backups.index', $backup), now()->addMinutes(10)
        );
    }

    public function createLinkForBackupId(string $BackupId)
    {
        $backup = Backup::firstOrFail('id', $BackupId);

        return URL::temporarySignedRoute(
            route('backups.index', $backup), now()->addMinutes(10)
        );
    }
}
