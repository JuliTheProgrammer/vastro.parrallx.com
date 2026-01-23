<?php

namespace App\Console\Commands\Vault;

use App\Actions\VaultAction;
use Illuminate\Console\Command;

class GetVaultsCommand extends Command
{
    protected $signature = 'dispatch:get-vaults';

    protected $description = 'Command description';

    public function handle(): void
    {
        $vaults = app(VaultAction::class)->getVaultsByUserId(1);

        ray($vaults);

        self::SUCCESS;
    }
}
