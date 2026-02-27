<?php

namespace App\Jobs\Folder;

use App\Models\Folder;
use App\Models\Vault;
use Aws\S3\S3Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateFolderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $name,
        protected string $storageClass,
        protected string $folderableType,
        protected int $folderableId,
        protected string $location,
        protected Vault $vault,
    ) {}

    public function handle(): void
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->location,
        ]);

        $awsPath = "{$this->name}/";

        $s3Client->putObject([
            'Bucket' => $this->vault->aws_bucket_name,
            'Key' => $awsPath,
            'Body' => '',
        ]);

        Folder::create([
            'vault_id' => $this->vault->id,
            'folderable_type' => $this->folderableType,
            'folderable_id' => $this->folderableId,
            'name' => $this->name,
            'location' => $this->location,
            'aws_path' => $awsPath,
            'storage_class' => $this->storageClass,
        ]);
    }
}
