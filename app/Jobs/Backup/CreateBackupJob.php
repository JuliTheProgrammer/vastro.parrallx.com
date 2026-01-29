<?php

namespace App\Jobs\Backup;

use App\Jobs\KMS\EncryptDataJob;
use App\Models\Backup;
use App\Models\StorageClass;
use App\Models\Vault;
use Aws\S3\S3Client;
use Aws\S3\S3Transfer\Models\UploadRequest;
use Aws\S3\S3Transfer\S3TransferManager;
use Illuminate\Auth\Access\Gate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // get the file content from the request
    public function __construct(protected string $storedPath, protected Vault $vault, protected array $meta) {}

    public function handle(): void
    {
        // Gate::authorize('upload', Backup::class);

        // EncryptDataJob::dispatch($this->storedPath);

        ray($this->meta);

        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->vault->region,
        ]);

        $transferManager = new S3TransferManager($s3Client);
        $absolutePath = Storage::path($this->storedPath);
        $key = $this->generateBackupName();

        // also put tags onto the object
        $uploadPromise = $transferManager->upload(
            new UploadRequest($absolutePath, [
                'Bucket' => $this->vault->aws_bucket_name,
                'Key' => $key, // the key is also with the folders, not only the filename
                'StorageClass' => $this->meta['storage_class'],
                'Tagging' => http_build_query([
                    'user_id' => Auth::id(),
                ]
                ),
            ])
        );

        ray($this->meta['storage_class']);

        $storageClassId = StorageClass::query()
            ->where('storage_class', $this->meta['storage_class'])
            ->firstOrFail()
            ->id;

        ray($storageClassId);

        // define the storage class, if user did select one, if not take the one from the folder

        Backup::create([
            'backupable_id' => $this->vault->id,
            'backupable_type' => get_class($this->vault),
            'user_id' => Auth::user()->id, // later change this dynamically
            'name' => $this->meta['original_name'] ?? 'backup',
            'path' => $key,
            'size_megaBytes' => $this->meta['size'] ?? null,
            'mime_type' => $this->meta['mime_type'] ?? null,
            'storage_class_id' => $storageClassId,
        ]);

        Storage::delete($this->storedPath);
    }

    public function generateBackupName(): string
    {
        return Str::uuid()->toString();
    }
}
