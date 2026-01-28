<?php

namespace App\Observers;

use App\Models\Vault;
use Illuminate\Support\Facades\Auth;

class VaultObserver
{
    /**
     * Handle the Vault "created" event.
     */
    public function created(Vault $vault): void
    {
        $user = Auth::user();
        $user->userStatistics()->vault_count++;
    }

    /**
     * Handle the Vault "updated" event.
     */
    public function updated(Vault $vault): void
    {
        //
    }

    /**
     * Handle the Vault "deleted" event.
     */
    public function deleted(Vault $vault): void
    {
        $user = Auth::user();
        $user->userStatistics()->vault_count--;
    }

    /**
     * Handle the Vault "restored" event.
     */
    public function restored(Vault $vault): void
    {
        //
    }

    /**
     * Handle the Vault "force deleted" event.
     */
    public function forceDeleted(Vault $vault): void
    {
        //
    }
}
