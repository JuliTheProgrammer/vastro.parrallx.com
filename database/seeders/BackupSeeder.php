<?php

namespace Database\Seeders;

use App\Models\Backup;
use App\Models\Folder;
use App\Models\Vault;
use Illuminate\Database\Seeder;

class BackupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vaults = Vault::query()->get();
        if ($vaults->isEmpty()) {
            $vaults = Vault::factory()->count(3)->create();
        }

        $vaults->each(function (Vault $vault) {
            Backup::factory()
                ->count(8)
                ->state([
                    'backupable_type' => Vault::class,
                    'backupable_id' => $vault->id,
                ])
                ->create();
        });

        $folders = Folder::query()->get();
        if ($folders->isEmpty()) {
            $folders = Folder::factory()->count(3)->create();
        }

        $folders->each(function (Folder $folder) {
            Backup::factory()
                ->count(8)
                ->state([
                    'backupable_type' => Folder::class,
                    'backupable_id' => $folder->id,
                ])
                ->create();
        });
    }
}
