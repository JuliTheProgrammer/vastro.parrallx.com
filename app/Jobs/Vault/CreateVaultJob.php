<?php

namespace App\Jobs\Vault;

use App\Models\Location;
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

        $bucket = $s3Client->createBucket([
            'Bucket' => $bucketName,
            'ObjectLockEnabledForBucket' => Arr::get($this->vaultData, 3),
        ]);

        $s3Client->waitUntil('BucketExists', ['Bucket' => $bucketName]);

        // bucket versioning
        if (Arr::get($this->vaultData, 2)) {
            $s3Client->putBucketVersioning([
                'Bucket' => $bucketName,
                'VersioningConfiguration' => [
                    'Status' => 'Enabled',
                ],
            ]);
        }

        // put tags on buckets

        ray($bucket);

        ray(Arr::get($bucket, 'BucketArn'));

        $location = Arr::get($this->vaultData, 1);

        $locationId = Location::where('code', $location)->firstOrFail()->id;

        ray($location);

        Vault::create([
            'user_id' => Auth::id(), // Later change to the current Auth user
            'name' => Arr::get($this->vaultData, 0),
            'aws_bucket_name' => $bucketName,
            'aws_bucket_arn' => Arr::get($bucket, 'BucketArn'),
            'worm_protection' => Arr::get($this->vaultData, 2),
            'delete_protection' => Arr::get($this->vaultData, 3),
            'location_id' => $locationId,
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

    protected function resolveLocationCode(string $code): string
    {
        return Location::query()
            ->where('code', $code)
            ->firstOrFail()
            ->code;
    }

    public function failed(Exception $exception)
    {
        activity('Request FAILED')
            ->log("request to create bucket failed {$exception->getMessage()}");

        // show error message to user
    }
}
