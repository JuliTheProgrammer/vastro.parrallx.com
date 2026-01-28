<?php

namespace App\Observers;

use App\Models\Backup;
use Illuminate\Support\Facades\Auth;

class BackupObserver
{
    /**
     * Handle the Backup "created" event.
     */
    public function created(Backup $backup): void
    {
        $user = Auth::user();
        $user->userStatistics()->total_stored_megaBytes += $backup->size;

        $user->userStatistics()->backup_count++;

        $user->userStatistics()->save();
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
        $user = Auth::user();
        $user->userStatistics()->total_stored_megaBytes -= $backup->size;

        $user->userStatistics()->backup_count--;

        $user->userStatistics()->save();
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
