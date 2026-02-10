<?php

namespace App\Actions;

use App\Models\Backup;
use App\Models\Link;
use App\Models\Location;
use App\Models\Vault;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use function DI\string;

// The link will have access to anything which belongs to the model for example vault -> folders -> backups
class LinkAction
{
    protected $path;

    protected $vault;

    protected $region;

    public function createLinkForBackup(Backup $backup)
    {
        return $this->buildPresignedUrl($backup);
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

        return string($request->getUri());
    }

    public function createLinkForBackupId(string $BackupId)
    {
        $backup = Backup::firstOrFail('id', $BackupId);

        return URL::temporarySignedRoute(
            route('backups.index', $backup), now()->addMinutes(10)
        );
    }

    public function createLinkForVaultId(string $VaultId)
    {
        $backup = Backup::firstOrFail('id', $VaultId);

        return URL::temporarySignedRoute(
            route('vaults.index', $backup), now()->addMinutes(10)
        );
    }
}
