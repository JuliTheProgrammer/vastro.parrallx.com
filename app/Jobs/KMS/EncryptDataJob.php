<?php

namespace App\Jobs\KMS;

use Aws\Kms\KmsClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EncryptDataJob implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $storedPath) {}

    public function handle(): void
    {
        $contents = file_get_contents($this->storedPath);

        $kmsClient = new KmsClient([
            'version' => 'default',
            'region' => config('services.aws.region'),
        ]);

        $kmsClient->encrypt([
            'KeyId' => config('services.kms.key_id'),
            'Plaintext' => $contents,
        ]);
    }
}
