<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\Vault;
use Illuminate\Database\Seeder;

class FolderSeeder extends Seeder
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
            Folder::factory()
                ->count(3)
                ->state([
                    'folderable_type' => Vault::class,
                    'folderable_id' => $vault->id,
                ])
                ->create();
        });

        $folders = Folder::query()->get();
        if ($folders->isEmpty()) {
            $folders = Folder::factory()->count(3)->create();
        }

        $folders->each(function (Folder $folder) {
            Folder::factory()
                ->count(3)
                ->state([
                    'folderable_type' => Folder::class,
                    'folderable_id' => $folder->id,
                ])
                ->create();
        });
    }
}
