<?php

use App\Jobs\Backup\GetPresignedURLJob;
use App\Models\Backup;
use Tests\TestCase;

uses(TestCase::class);

it('passes the resolved backup into the presigned url builder', function () {
    $backup = new Backup;

    $job = new class(123, $backup) extends GetPresignedURLJob
    {
        public mixed $resolvedBackup;

        public function __construct(int $backupId, protected Backup $backup)
        {
            parent::__construct($backupId);
        }

        protected function resolveBackup(): Backup
        {
            return $this->backup;
        }

        protected function buildPresignedUrl(Backup $backup): string
        {
            $this->resolvedBackup = $backup;

            return 'https://example.com/presigned';
        }
    };

    $job->handle();

    expect($job->resolvedBackup)->toBe($backup);
});
