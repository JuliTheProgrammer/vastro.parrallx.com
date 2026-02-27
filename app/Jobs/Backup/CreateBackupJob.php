<?php

namespace App\Jobs\Backup;

use App\Jobs\KMS\EncryptDataJob;
use App\Models\Backup;
use App\Models\Location;
use App\Models\StorageClass;
use App\Models\Vault;
use Aws\S3\S3Client;
use Aws\S3\S3Transfer\Models\UploadRequest;
use Aws\S3\S3Transfer\S3TransferManager;
use Aws\Sdk;
use Illuminate\Auth\Access\Gate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // get the file content from the request
    public function __construct(protected string $storedPath, protected Vault $vault, protected array $meta, private bool $aiAnalyses) {}

    public function handle(): void
    {
        // Gate::authorize('upload', Backup::class);

        // EncryptDataJob::dispatch($this->storedPath);

        $region = $this->resolveRegion();
        $sdk = new Sdk([
            'region' => $region,
        ]);

        $stsClient = $sdk->createSts();

        // assume role

        $result = $stsClient->getSessionToken();

        ray($this->meta);

        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => [
                'key' => $result['Credentials']['AccessKeyId'],
                'secret' => $result['Credentials']['SecretAccessKey'],
                'token' => $result['Credentials']['SessionToken'],
            ],
        ]);

        $transferManager = new S3TransferManager($s3Client);
        $absolutePath = Storage::path($this->storedPath);
        $key = $this->generateBackupName();

        // also put tags onto the object
        try {
            $uploadPromise = $transferManager->upload(
                new UploadRequest($absolutePath, [
                    'Bucket' => $this->vault->aws_bucket_name,
                    'Key' => $key, // the key is also with the folders, not only the filename
                    'StorageClass' => $this->resolveStorageClass(),
                    'Tagging' => http_build_query([
                        'user_id' => $this->resolveUserId(),
                    ]),
                ])
            );

            $uploadPromise->wait();
        } catch (\Throwable $exception) {
            Log::error('Backup upload failed', [
                'vault_id' => $this->vault->id,
                'bucket' => $this->vault->aws_bucket_name,
                'path' => $this->storedPath,
                'region' => $region,
                'storage_class' => $this->resolveStorageClass(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        $storageClassId = StorageClass::query()
            ->where('storage_class', $this->resolveStorageClass())
            ->firstOrFail()
            ->id;

        ray($storageClassId);

        // define the storage class, if user did select one, if not take the one from the folder
        $backup = Backup::create([
            'backupable_id' => $this->vault->id,
            'backupable_type' => get_class($this->vault),
            'user_id' => $this->resolveUserId(),
            'name' => $this->meta['original_name'] ?? 'backup',
            'path' => $key,
            'size_megaBytes' => $this->meta['size'] ?? null,
            'mime_type' => $this->meta['mime_type'] ?? null,
            'storage_class_id' => $storageClassId,
        ]);

        // check if the user is allowed to analyse the image based on credits
        if ($backup->user->userStatistics->used_api_tokens >= $backup->user->userStatistics->max_api_tokens) {
            // TODO - make a custom exception
            return;
        }

        if ($this->aiAnalyses) {
            AnalyseImageJob::dispatch($this->storedPath, $backup->id);
        }

        // delete backup was moved after image anaalysis
    }

    public function generateBackupName(): string
    {
        return Str::uuid()->toString();
    }

    protected function resolveStorageClass(): string
    {
        return $this->meta['storage_class'] ?? 'STANDARD';
    }

    protected function resolveRegion(): string
    {
        if ($this->vault->location_id) {
            $location = Location::find($this->vault->location_id);
            if ($location?->code) {
                return $location->code;
            }
        }

        return config('filesystems.disks.s3.region');
    }

    protected function resolveUserId(): int
    {
        return (int) ($this->meta['user_id'] ?? $this->vault->user_id);
    }
}
