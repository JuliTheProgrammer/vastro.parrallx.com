<?php

namespace App\Jobs\Backup;

use App\Models\Backup;
use App\Models\Link;
use App\Models\Location;
use App\Models\Vault;
use Aws\S3\S3Client;
use Aws\Sdk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function DI\string;

// Will get called from a controller -> route

// CURRENTY NOT USED -> ACTION
class GetPresignedURLJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;

    protected $vault;

    protected $region;

    public function __construct(protected int $backupId) {}

    public function handle(): void
    {
        $backup = $this->resolveBackup();
        $this->buildPresignedUrl($backup);
    }

    protected function resolveBackup(): Backup
    {
        return Backup::findOrFail($this->backupId);
    }

    protected function buildPresignedUrl(Backup $backup): string
    {
        $this->path = $backup->path;
        $vaultId = $backup->backupable_id; // can later also be a folder but for now only vaults

        $this->vault = Vault::findOrFail($vaultId);

        $locationId = $this->vault->location_id;

        $this->region = Location::findOrFail($locationId)->code;

        ray($this->vault);

        //        $sdk = new Sdk([
        //            'region' => Arr::get($this->vaultData, 1),
        //        ]);
        //
        //        $stsClient = $sdk->createSts();

        // assume role
        //
        //        $result = $stsClient->getSessionToken();

        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->region,
            //            'credentials' => [
            //                'key' => $result['Credentials']['AccessKeyId'],
            //                'secret' => $result['Credentials']['SecretAccessKey'],
            //                'token' => $result['Credentials']['SessionToken'],
            //            ],
        ]);

        $request = $s3Client->createPresignedRequest(
            $s3Client->getCommand('GetObject', [
                'Bucket' => $this->vault->aws_bucket_name,
                'Key' => $this->path,
            ]),
            '+1 hour'
        );

        ray(string($request->getUri()));

        // Create a Link

        Link::create([
            'user_id' => $this->vault->user_id,
            'linkable_type' => Backup::class,
            'linkable_id' => $backup->id,
            'name' => Str::uuid()->toString(),
            'expires_at' => now()->addHour(),
        ]);
    }
}
