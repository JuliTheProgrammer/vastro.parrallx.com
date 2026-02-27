<?php

namespace App\Actions;

use App\Jobs\Backup\CreateBackupJob;
use App\Models\Backup;
use App\Models\Location;
use App\Models\Vault;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class BackupActions
{
    public function uploadBackup(string $storedPath, Vault $vault, array $meta, bool $aiAnalyses): void
    {
        CreateBackupJob::dispatch($storedPath, $vault, $meta, $aiAnalyses);
    }

    public function deleteBackup(Backup $backup)
    {
        $vaultId = $backup->backupable_id;
        $vault = Vault::find($vaultId);
        $bucket = $vault->aws_bucket_name;
        $locationId = $vault->location_id;
        $region = Location::findOrFail($locationId)->code;
        $keyname = $backup->path;

        $s3 = new S3Client([
            'version' => 'latest',
            'region' => $region,
        ]);

        try {
            $result = $s3->deleteObject([
                'Bucket' => $bucket,
                'Key' => $keyname,
            ]);
            ray($result);
        } catch (S3Exception $e) {
            exit('Error: '.$e->getAwsErrorMessage().PHP_EOL);
        }
    }

    public function getBackupContents() {}
}
