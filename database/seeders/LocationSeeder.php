<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $locations = [
            ['code' => 'us-east-1', 'name' => 'US East (N. Virginia)', 'AZs' => 6, 'geography' => 'United States of America'],
            ['code' => 'us-east-2', 'name' => 'US East (Ohio)', 'AZs' => 3, 'geography' => 'United States of America'],
            ['code' => 'us-west-1', 'name' => 'US West (N. California)', 'AZs' => 3, 'geography' => 'United States of America'],
            ['code' => 'us-west-2', 'name' => 'US West (Oregon)', 'AZs' => 4, 'geography' => 'United States of America'],
            ['code' => 'af-south-1', 'name' => 'Africa (Cape Town)', 'AZs' => 3, 'geography' => 'South Africa'],
            ['code' => 'ap-east-1', 'name' => 'Asia Pacific (Hong Kong)', 'AZs' => 3, 'geography' => 'Hong Kong'],
            ['code' => 'ap-south-2', 'name' => 'Asia Pacific (Hyderabad)', 'AZs' => 3, 'geography' => 'India'],
            ['code' => 'ap-southeast-3', 'name' => 'Asia Pacific (Jakarta)', 'AZs' => 3, 'geography' => 'Indonesia'],
            ['code' => 'ap-southeast-5', 'name' => 'Asia Pacific (Malaysia)', 'AZs' => 3, 'geography' => 'Malaysia'],
            ['code' => 'ap-southeast-4', 'name' => 'Asia Pacific (Melbourne)', 'AZs' => 3, 'geography' => 'Australia'],
            ['code' => 'ap-south-1', 'name' => 'Asia Pacific (Mumbai)', 'AZs' => 3, 'geography' => 'India'],
            ['code' => 'ap-southeast-6', 'name' => 'Asia Pacific (New Zealand)', 'AZs' => 3, 'geography' => 'New Zealand'],
            ['code' => 'ap-northeast-3', 'name' => 'Asia Pacific (Osaka)', 'AZs' => 3, 'geography' => 'Japan'],
            ['code' => 'ap-northeast-2', 'name' => 'Asia Pacific (Seoul)', 'AZs' => 4, 'geography' => 'South Korea'],
            ['code' => 'ap-southeast-1', 'name' => 'Asia Pacific (Singapore)', 'AZs' => 3, 'geography' => 'Singapore'],
            ['code' => 'ap-southeast-2', 'name' => 'Asia Pacific (Sydney)', 'AZs' => 3, 'geography' => 'Australia'],
            ['code' => 'ap-east-2', 'name' => 'Asia Pacific (Taipei)', 'AZs' => 3, 'geography' => 'Taiwan'],
            ['code' => 'ap-southeast-7', 'name' => 'Asia Pacific (Thailand)', 'AZs' => 3, 'geography' => 'Thailand'],
            ['code' => 'ap-northeast-1', 'name' => 'Asia Pacific (Tokyo)', 'AZs' => 4, 'geography' => 'Japan'],
            ['code' => 'ca-central-1', 'name' => 'Canada (Central)', 'AZs' => 3, 'geography' => 'Canada'],
            ['code' => 'ca-west-1', 'name' => 'Canada West (Calgary)', 'AZs' => 3, 'geography' => 'Canada'],
            ['code' => 'eu-central-1', 'name' => 'Europe (Frankfurt)', 'AZs' => 3, 'geography' => 'Germany'],
            ['code' => 'eu-west-1', 'name' => 'Europe (Ireland)', 'AZs' => 3, 'geography' => 'Ireland'],
            ['code' => 'eu-west-2', 'name' => 'Europe (London)', 'AZs' => 3, 'geography' => 'United Kingdom'],
            ['code' => 'eu-south-1', 'name' => 'Europe (Milan)', 'AZs' => 3, 'geography' => 'Italy'],
            ['code' => 'eu-west-3', 'name' => 'Europe (Paris)', 'AZs' => 3, 'geography' => 'France'],
            ['code' => 'eu-south-2', 'name' => 'Europe (Spain)', 'AZs' => 3, 'geography' => 'Spain'],
            ['code' => 'eu-north-1', 'name' => 'Europe (Stockholm)', 'AZs' => 3, 'geography' => 'Sweden'],
            ['code' => 'eu-central-2', 'name' => 'Europe (Zurich)', 'AZs' => 3, 'geography' => 'Switzerland'],
            ['code' => 'il-central-1', 'name' => 'Israel (Tel Aviv)', 'AZs' => 3, 'geography' => 'Israel'],
            ['code' => 'mx-central-1', 'name' => 'Mexico (Central)', 'AZs' => 3, 'geography' => 'Mexico'],
            ['code' => 'me-south-1', 'name' => 'Middle East (Bahrain)', 'AZs' => 3, 'geography' => 'Bahrain'],
            ['code' => 'me-central-1', 'name' => 'Middle East (UAE)', 'AZs' => 3, 'geography' => 'United Arab Emirates'],
            ['code' => 'sa-east-1', 'name' => 'South America (São Paulo)', 'AZs' => 3, 'geography' => 'Brazil'],
        ];

        $payload = collect($locations)->map(function (array $location) use ($now) {
            return [
                ...$location,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        Location::query()->upsert(
            $payload,
            ['code'],
            ['name', 'AZs', 'geography', 'active', 'updated_at']
        );
    }
}
