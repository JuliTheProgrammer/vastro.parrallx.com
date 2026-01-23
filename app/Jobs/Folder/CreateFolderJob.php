<?php

namespace App\Jobs\Folder;

use App\Models\Folder;
use App\Models\Vault;
use Aws\S3\S3Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateFolderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected string $name, protected string $storageClass, protected $folderable_type, protected $folderable_id, protected string $location, protected Vault $vault) {}

    public function handle(): void
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->location,
        ]);

        $s3Client->putObject([
            'Bucket' => $this->vault->aws_bucket_name,
            'Key' => "{$this->name}/",
            'Body' => '',
        ]);

        $folder = Folder::create([
            'folderable_type' => $this->folderable_type,
            'folderable_id' => $this->folderable_id,
            'name' => $this->name,
            'storage_class' => $this->storageClass,
        ]);
    }
}
