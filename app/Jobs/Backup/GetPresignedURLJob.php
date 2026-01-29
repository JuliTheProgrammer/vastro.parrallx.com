<?php

namespace App\Jobs\Backup;

use App\Models\Backup;
use App\Models\Location;
use App\Models\Vault;
use Aws\S3\S3Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use function DI\string;

// Will get called from a controller -> route
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

        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->region,
        ]);

        $request = $s3Client->createPresignedRequest(
            $s3Client->getCommand('GetObject', [
                'Bucket' => $this->vault->aws_bucket_name,
                'Key' => $this->path,
            ]),
            '+1 hour'
        );

        ray (string($request->getUri()));

        return (string) $request->getUri();
    }
}
