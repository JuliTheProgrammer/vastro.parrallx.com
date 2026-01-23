<?php

namespace App\Jobs\Backup;

use App\Models\Vault;
use Aws\S3\S3Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Will get called from a controller -> route
class GetBackupContentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(string $path, Vault $vault) {}

    public function handle(): void
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => 'us-west-2',
        ]);

        $file = $s3Client->getObject([
            'Bucket' => $this->vault->name,
            'Key' => $this->path,
        ]);

        $fileBody = $file->get('Body');

        $fileBody->rewind();

    }
}
