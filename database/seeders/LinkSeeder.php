<?php

namespace Database\Seeders;

use App\Models\Backup;
use App\Models\Link;
use App\Models\Vault;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vaults = Vault::query()->get();
        if ($vaults->isEmpty()) {
            $vaults = Vault::factory()->count(2)->create();
        }

        $backups = Backup::query()->get();
        if ($backups->isEmpty()) {
            $backups = Backup::factory()->count(4)->create();
        }

        $vaults->each(function (Vault $vault) {
            Link::factory()->state([
                'linkable_type' => Vault::class,
                'linkable_id' => $vault->id,
            ])->create();
        });

        $backups->each(function (Backup $backup) {
            Link::factory()->state([
                'linkable_type' => Backup::class,
                'linkable_id' => $backup->id,
            ])->create();
        });
    }
}
