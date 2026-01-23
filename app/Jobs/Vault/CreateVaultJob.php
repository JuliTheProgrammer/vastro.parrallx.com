<?php

namespace App\Jobs\Vault;

use App\Models\User;
use App\Models\Vault;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateVaultJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 10;

    public $tries = 3;

    public $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $vaultData) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ray($this->vaultData);
        $s3Client = new S3Client(['region' => Arr::get($this->vaultData, 1)]); // change the region dynamic

        activity('Request POST')
            ->log('request to create bucket');

        $bucketName = $this->generateBucketName(Arr::get($this->vaultData, 0));

        // put tags on the bucket as well
        $bucket = $s3Client->createBucket([
            'Bucket' => $bucketName,
            'ObjectLockEnabledForBucket' => Arr::get($this->vaultData, 3),
        ]);

        $s3Client->waitUntil('BucketExists', ['Bucket' => $bucketName]);

        // bucket versioning
        if (Arr::get($this->vaultData, 4)) {
            $s3Client->putBucketVersioning([
                'Bucket' => $bucketName,
                'VersioningConfiguration' => [
                    'Status' => 'Enabled',
                ],
            ]);
        }

        ray($bucket);

        ray(Arr::get($bucket, 'BucketArn'));

        Vault::create([
            'user_id' => Auth::id() ?? 1, // Later change to the current Auth user
            'name' => Arr::get($this->vaultData, 0),
            'aws_bucket_name' => $bucketName,
            'aws_bucket_arn' => Arr::get($bucket, 'BucketArn'),
            'location' => Arr::get($this->vaultData, 1),
        ]);

    }

    public function generateBucketName($vaultName): string
    {
        // the uuid should be the uuid from the Auth user
        $user = Auth::user()->id;

        $uuid = Str::uuid()->toString();

        // take the user uuid as a prefix, currently substituted through a Str::uuid
        return "{$user}-{$uuid}";
    }

    public function failed(Exception $exception)
    {
        activity('Request FAILED')
            ->log("request to create bucket failed {$exception->getMessage()}");

        // show error message to user
    }
}
