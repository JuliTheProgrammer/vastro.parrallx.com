<?php

use App\Jobs\Vault\CreateVaultJob;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('resolves a vault location code from the locations table', function () {
    Location::unguarded(function (): void {
        Location::create([
            'code' => 'us-east-1',
            'name' => 'US East (N. Virginia)',
            'AZs' => 6,
            'geography' => 'United States of America',
            'active' => true,
        ]);
    });

    $job = new class(['Vault 1', 'us-east-1', true, false, true]) extends CreateVaultJob
    {
        public function resolveLocationCodeForTest(string $code): string
        {
            return $this->resolveLocationCode($code);
        }
    };

    expect($job->resolveLocationCodeForTest('us-east-1'))->toBe('us-east-1');
});
