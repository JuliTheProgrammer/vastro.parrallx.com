<?php

namespace App\Jobs\Backup;

use App\Models\Backup;
use App\Models\Link;
use App\Models\Vault;
use Aws\S3\S3Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class GetPresignedURLJob implements ShouldQueue
{
    use Queueable;

    public function __construct(protected int $backupId) {}

    public function handle(): void
    {
        $backup = Backup::findOrFail($this->backupId);
        $presignedUrl = $this->buildPresignedUrl($backup);

        Link::create([
            'user_id' => $backup->backupable instanceof Vault
                ? $backup->backupable->user_id
                : $backup->user_id,
            'linkable_type' => Backup::class,
            'linkable_id' => $backup->id,
            'name' => Str::uuid()->toString(),
            'url' => $presignedUrl,
            'expires_at' => now()->addHour(),
        ]);
    }

    protected function buildPresignedUrl(Backup $backup): string
    {
        $vault = Vault::findOrFail($backup->backupable_id);
        $region = $vault->location()->value('code');

        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $region,
        ]);

        $request = $s3Client->createPresignedRequest(
            $s3Client->getCommand('GetObject', [
                'Bucket' => $vault->aws_bucket_name,
                'Key' => $backup->path,
            ]),
            '+1 hour'
        );

        return (string) $request->getUri();
    }
}
