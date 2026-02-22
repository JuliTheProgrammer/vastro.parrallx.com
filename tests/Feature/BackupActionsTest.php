<?php

use App\Actions\BackupActions;
use App\Jobs\Backup\AnalyseImageJob;
use App\Jobs\Backup\CreateBackupJob;
use App\Models\Vault;
use Illuminate\Support\Facades\Bus;

it('dispatches analyse image and create backup jobs in local env', function () {
    Bus::fake();
    config(['app.env' => 'local']);

    $vault = Vault::factory()->make();
    $storedPath = 'backups/example.jpg';
    $meta = ['name' => 'example.jpg'];

    app(BackupActions::class)->uploadBackup($storedPath, $vault, $meta);

    Bus::assertDispatched(AnalyseImageJob::class);
    Bus::assertDispatched(CreateBackupJob::class);
});
