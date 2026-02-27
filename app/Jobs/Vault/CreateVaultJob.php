<?php

namespace App\Jobs\Vault;

use App\Models\Location;
use App\Models\Vault;
use Aws\S3\S3Client;
use Aws\Sdk;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateVaultJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 120;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        protected string $name,
        protected string $region,
        protected bool $wormProtection,
        protected bool $deleteProtection,
        protected int $userId,
    ) {}

    public function handle(): void
    {
        $sdk = new Sdk([
            'region' => $this->region,
        ]);

        $stsClient = $sdk->createSts();
        $result = $stsClient->getSessionToken();

        $s3Client = new S3Client([
            'region' => $this->region,
            'credentials' => [
                'key' => $result['Credentials']['AccessKeyId'],
                'secret' => $result['Credentials']['SecretAccessKey'],
                'token' => $result['Credentials']['SessionToken'],
            ],
        ]);

        activity('Request POST')
            ->log('request to create bucket');

        $bucketName = $this->generateBucketName();

        $bucket = $s3Client->createBucket([
            'Bucket' => $bucketName,
            'ObjectLockEnabledForBucket' => $this->deleteProtection,
        ]);

        $s3Client->waitUntil('BucketExists', ['Bucket' => $bucketName]);

        if ($this->wormProtection) {
            $s3Client->putBucketVersioning([
                'Bucket' => $bucketName,
                'VersioningConfiguration' => [
                    'Status' => 'Enabled',
                ],
            ]);
        }

        $locationId = Location::where('code', $this->region)->firstOrFail()->id;

        Vault::create([
            'user_id' => $this->userId,
            'name' => $this->name,
            'aws_bucket_name' => $bucketName,
            'aws_bucket_arn' => $bucket['BucketArn'] ?? null,
            'worm_protection' => $this->wormProtection,
            'delete_protection' => $this->deleteProtection,
            'location_id' => $locationId,
        ]);
    }

    protected function generateBucketName(): string
    {
        return "vault-{$this->userId}-".now()->timestamp;
    }

    public function failed(Exception $exception): void
    {
        activity('Request FAILED')
            ->log("request to create bucket failed {$exception->getMessage()}");
    }
}
