<?php

namespace App\Jobs\KMS;

use Aws\Kms\KmsClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EncryptDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(string $storedPath)
    {

        $contents = file_get_contents($storedPath);

        $kmsClient = new KmsClient([
            'version' => 'default',
            'region' => 'us-east-2',
        ]);

        $keyId = config('services.kms.key_id');

        $result = $kmsClient->encrypt([
            'KeyId' => $keyId,
            'Plaintext' => $contents,
        ]);
    }

    public function handle(): void {}
}
