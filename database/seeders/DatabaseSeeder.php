<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //        $this->call([
        //            VaultSeeder::class,
        //            FolderSeeder::class,
        //            BackupSeeder::class,
        //            LinkSeeder::class,
        //            InvoiceSeeder::class,
        //        ]);

        User::factory()->create([
            'name' => 'Vastro',
            'email' => 'info@vastro.dev',
            'password' => 'password',
        ]);
    }
}
