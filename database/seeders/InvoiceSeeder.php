<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->get();
        if ($users->isEmpty()) {
            $users = User::factory()->count(3)->create();
        }

        $users->each(function (User $user) {
            Invoice::factory()
                ->count(5)
                ->state(['user_id' => $user->id])
                ->create();
        });
    }
}
