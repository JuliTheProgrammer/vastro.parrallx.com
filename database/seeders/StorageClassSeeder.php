<?php

namespace Database\Seeders;

use App\Models\StorageClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class StorageClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $storageClasses = [
            ['name' => 'Standard', 'storage_class' => 'STANDARD'],
            ['name' => 'Standard - Infrequent Access', 'storage_class' => 'STANDARD_IA'],
            ['name' => 'One Zone - Infrequent Access', 'storage_class' => 'ONEZONE_IA'],
            ['name' => 'Glacier Instant Retrieval', 'storage_class' => 'GLACIER_IR'],
            ['name' => 'Deep Archive', 'storage_class' => 'DEEP_ARCHIVE'],
            ['name' => 'Intelligent Tiering', 'storage_class' => 'INTELLIGENT_TIERING'],
        ];

        foreach ($storageClasses as $storageClass) {
            StorageClass::query()->updateOrCreate(
                ['storage_class' => $storageClass['storage_class']],
                [
                    'name' => $storageClass['name'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
