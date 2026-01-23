<?php

namespace App\Console\Commands\Vault;

use App\Actions\VaultAction;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CreateVaultCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:create-vault {vault-name} {vault-location}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new VaultAction';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vaultName = $this->argument('vault-name');
        $vaultLocation = $this->argument('vault-location');

        $validator = Validator::make([
            'vault-name' => $vaultName,
            'vault-location' => $vaultLocation,
        ], [
            'vault-name' => ['required', 'string', 'max:255'],
            'vault-location' => ['required', 'string', 'max:255'],
        ]);

        $validated = $validator->validate();

        if ($validator->fails()) {
            return self::FAILURE;
        }

        app(VaultAction::class)->createVault(Arr::get($validated, 'vault-name'), Arr::get($validated, 'vault-location'));

        return self::SUCCESS;

    }
}
