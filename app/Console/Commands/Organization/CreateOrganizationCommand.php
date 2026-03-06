<?php

namespace App\Console\Commands\Organization;

use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CreateOrganizationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:create-organization
     {name}
     {logo?}
     {website?}
     {phone?}
     {address?}
     {city?}
     {state?}
     {postal_code?}
     {country?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new Organization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        $validator = Validator::make([
            'name' => $name,
        ],
            [
                'name' => ['required', 'string', 'max:255', 'unique:organizations,name'],
            ]);

        $validated = $validator->validate();

        abort_if($validator->fails(), 401);

        $organization = Organization::create([
            'name' => Arr::get($validated, 'name', ''),
        ]);

        abort_if(! $organization, 401);

        return self::SUCCESS;
    }
}
