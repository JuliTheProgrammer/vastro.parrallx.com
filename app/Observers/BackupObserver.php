<?php

namespace App\Observers;

use App\Helper\MimeHelper;
use App\Models\Backup;
use Illuminate\Support\Facades\Auth;

class BackupObserver
{
    public function creating(Backup $backup): void
    {
        $readableExtension = MimeHelper::convertMimeType($backup->mime_type);
        $backup->mime_type_readable = $readableExtension;
    }

    /**
     * Handle the Backup "created" event.
     */
    public function created(Backup $backup): void
    {
        $user = Auth::user();

        $user->userStatistics()->increment('total_stored_bytes', $backup->size_bytes);

        $user->userStatistics()->increment('backup_count');
    }

    /**
     * Handle the Backup "updated" event.
     */
    public function updated(Backup $backup): void
    {
        //
    }

    /**
     * Handle the Backup "deleted" event.
     */
    public function deleted(Backup $backup): void
    {
        //        $user = Auth::user();
        //        $user->userStatistics()->total_stored_megaBytes -= $backup->size;
        //
        //        $user->userStatistics()->backup_count--;
        //
        //        $user->userStatistics()->save();
    }

    /**
     * Handle the Backup "restored" event.
     */
    public function restored(Backup $backup): void
    {
        //
    }

    /**
     * Handle the Backup "force deleted" event.
     */
    public function forceDeleted(Backup $backup): void
    {
        //
    }
}
