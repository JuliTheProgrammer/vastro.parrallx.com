<?php

namespace App\Actions;

use App\Jobs\Vault\CreateVaultJob;
use App\Jobs\Vault\GetVaultJob;
use App\Models\User;

class VaultAction
{
    public function createVault($vaultName, $vaultLocation, $wormProtection, $deleteProtection)
    {
        ray('Action Called');
        $data = [$vaultName, $vaultLocation, $wormProtection, $deleteProtection];
        if (app()->environment('local')) {
            CreateVaultJob::dispatchSync($data);

            return;
        }

        dispatch(new CreateVaultJob($data));
    }

    // Get vaults from S3 used to sync
    public function syncVaultsFromS3()
    {
        GetVaultJob::dispatch();
    }

    // Get vaults for each individual user
    public function getVaultsByUser($user): ?array
    {
        $user = User::where('id', $user->id)->firstOrFail();
        $vaults = $user->vaults()->get();

        return $vaults;
    }

    public function getVaultsByUserId($userId)
    {
        $user = User::where('id', $userId)->firstOrFail();
        $vaults = $user->vaults()->get();

        return $vaults;
    }
}
