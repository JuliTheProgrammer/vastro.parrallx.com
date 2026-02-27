<?php

namespace App\Jobs\Backup;

use App\Exceptions\NotEnoughTokensException;
use App\Models\Backup;
use App\Models\StorageClass;
use App\Models\Vault;
use Aws\S3\S3Client;
use Aws\S3\S3Transfer\Models\UploadRequest;
use Aws\S3\S3Transfer\S3TransferManager;
use Aws\Sdk;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $storedPath,
        protected Vault $vault,
        protected array $meta,
        private bool $aiAnalyses,
    ) {}

    public function handle(): void
    {
        $region = $this->resolveRegion();

        $sdk = new Sdk(['region' => $region]);
        $stsClient = $sdk->createSts();
        $result = $stsClient->getSessionToken();

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
        $storageClass = $this->resolveStorageClass();

        try {
            $uploadPromise = $transferManager->upload(
                new UploadRequest($absolutePath, [
                    'Bucket' => $this->vault->aws_bucket_name,
                    'Key' => $key,
                    'StorageClass' => $storageClass,
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
                'storage_class' => $storageClass,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        $storageClassId = StorageClass::query()
            ->where('storage_class', $storageClass)
            ->firstOrFail()
            ->id;

        $backup = Backup::create([
            'backupable_id' => $this->vault->id,
            'backupable_type' => $this->vault::class,
            'user_id' => $this->resolveUserId(),
            'storage_class_id' => $storageClassId,
            'name' => $this->meta['original_name'] ?? 'backup',
            'path' => $key,
            'size_megaBytes' => $this->meta['size'] ?? null,
            'mime_type' => $this->meta['mime_type'] ?? null,
            'mime_type_readable' => $this->meta['mime_type_readable'] ?? $this->meta['mime_type'] ?? null,
        ]);

        $backup->load('user.userStatistics');

        throw_if($backup->user->userStatistics->hasEnoughTokens(), NotEnoughTokensException::class);

        if ($this->aiAnalyses) {
            AnalyseImageJob::dispatch($this->storedPath, $backup->id);
        }
    }

    protected function generateBackupName(): string
    {
        return Str::uuid()->toString();
    }

    protected function resolveStorageClass(): string
    {
        return $this->meta['storage_class'] ?? 'STANDARD';
    }

    protected function resolveRegion(): string
    {
        return $this->vault->location()->value('code')
            ?? config('filesystems.disks.s3.region');
    }

    protected function resolveUserId(): int
    {
        return (int) ($this->meta['user_id'] ?? $this->vault->user_id);
    }
}
