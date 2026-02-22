<?php

use App\Jobs\Backup\AnalyseImageJob;
use App\Models\Backup;
use App\Models\Location;
use App\Models\StorageClass;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Support\Facades\Storage;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Structured\Response as StructuredResponse;
use Prism\Prism\ValueObjects\Meta;
use Prism\Prism\ValueObjects\Usage;

it('stores analysis results on the backup', function () {
    Storage::fake();

    $storedPath = 'backups/example.jpg';
    Storage::put($storedPath, 'fake-image');

    $storageClass = StorageClass::factory()->create([
        'name' => 'Standard',
        'storage_class' => 'STANDARD',
    ]);
    $user = User::factory()->create();
    $location = Location::factory()->create();

    $vault = Vault::query()->create([
        'uuid' => fake()->uuid(),
        'user_id' => $user->id,
        'name' => fake()->unique()->word(),
        'aws_bucket_name' => fake()->word(),
        'aws_bucket_arn' => fake()->uuid(),
        'location_id' => $location->id,
        'worm_protection' => true,
        'delete_protection' => false,
        'kms_encryption' => false,
        'kms_arn' => null,
    ]);

    $backup = Backup::factory()->create([
        'storage_class_id' => $storageClass->id,
        'user_id' => $user->id,
        'backupable_type' => Vault::class,
        'backupable_id' => $vault->id,
        'name' => 'example.jpg',
        'path' => fake()->uuid(),
        'mime_type' => 'image/jpeg',
        'mime_type_readable' => 'JPEG Image',
        'size_megaBytes' => 2,
    ]);

    Prism::fake([
        new StructuredResponse(
            steps: collect([]),
            text: '',
            structured: [
                'description' => 'A test image',
                'tags' => ['test', 'image'],
            ],
            finishReason: FinishReason::Stop,
            usage: new Usage(0, 0),
            meta: new Meta('fake', 'fake'),
            additionalContent: [],
        ),
    ]);

    (new AnalyseImageJob($storedPath, $backup->id))->handle();

    $backup->refresh();

    expect($backup->backupAnalysis)->not->toBeNull();
    expect($backup->backupAnalysis->description)->toBe('A test image');
    expect($backup->backupAnalysis->tags)->toBe(['test', 'image']);
});
