<?php

namespace App\Jobs\Vault;

use Aws\S3\S3Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetVaultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function construct() {}

    public function handle(): void
    {

        $s3Client = new S3Client([]);

        $buckets = $s3Client->listBuckets();

        ray($buckets);
    }
}
