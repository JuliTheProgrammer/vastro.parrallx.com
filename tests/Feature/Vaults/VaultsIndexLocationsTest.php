<?php

use App\Models\Location;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;

it('provides active locations to the vaults index view', function () {
    $user = User::factory()->create();

    Location::factory()->create([
        'code' => 'us-east-1',
        'name' => 'US East (N. Virginia)',
        'active' => true,
    ]);
    Location::factory()->create([
        'code' => 'eu-central-1',
        'name' => 'Europe (Frankfurt)',
        'active' => true,
    ]);
    Location::factory()->create([
        'code' => 'inactive-1',
        'name' => 'Inactive Location',
        'active' => false,
    ]);

    actingAs($user);

    $this->get('/vaults')->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('vaults/index')
            ->has('locations', 2)
    );
});
