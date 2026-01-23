<?php

namespace App\Jobs\Backup;

use App\Models\Backup;
use App\Models\Vault;
use Aws\S3\S3Client;
use Aws\S3\S3Transfer\Models\UploadRequest;
use Aws\S3\S3Transfer\S3TransferManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // get the file content from the request
    public function __construct(protected ?array $path, protected $file, protected ?Vault $vault) {}

    public function handle(): void
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => 'us-west-2',
        ]);

        $transferManager = new S3TransferManager($s3Client);

        // also put tags onto the object
        $uploadPromise = $transferManager->upload(
            new UploadRequest('path the local file', [
                'Bucket' => $this->vault ?? Arr::get($this->vault, 'bucket'),
                'Key' => Arr::get($this->path, 'key'),
            ])
        );

        $result = $uploadPromise->wait();

        Backup::create([
            'vault_id' => $this->vault->id,
            'user_id' => 1, // later change this dynamically
            'name' => $this->file->name,
            'path' => $this->generateBackupName(),
            'size' => $this->file->size,
            'mime_type' => $this->file->mime_type,
        ]);
    }

    public function generateBackupName(): string
    {
        return Str::uuid()->toString();
    }
}
