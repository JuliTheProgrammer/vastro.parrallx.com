<?php

namespace App\Actions;

use App\Jobs\Vault\CreateVaultJob;
use App\Jobs\Vault\GetVaultJob;
use App\Models\User;

class VaultAction
{
    public function createVault(string $vaultName, string $vaultLocation, bool $wormProtection, bool $deleteProtection, int $userId): void
    {
        $job = new CreateVaultJob($vaultName, $vaultLocation, $wormProtection, $deleteProtection, $userId);

        if (app()->environment('local')) {
            CreateVaultJob::dispatchSync($vaultName, $vaultLocation, $wormProtection, $deleteProtection, $userId);

            return;
        }

        dispatch($job);
    }

    public function syncVaultsFromS3(): void
    {
        GetVaultJob::dispatch();
    }

    public function getVaultsByUser(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->vaults()->get();
    }

    public function getVaultsByUserId(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return User::findOrFail($userId)->vaults()->get();
    }
}
