<?php

namespace Database\Seeders;

use App\Enums\OrganizationRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OrganizationPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        foreach (OrganizationRole::allPermissions() as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Create roles and assign their permissions
        foreach (OrganizationRole::cases() as $role) {
            $spatieRole = Role::firstOrCreate(['name' => $role->value]);
            $spatieRole->syncPermissions($role->permissions());
        }

        $this->command->info('Organization permissions and roles seeded successfully.');
        $this->command->table(
            ['Role', 'Permissions'],
            collect(OrganizationRole::cases())->map(fn (OrganizationRole $role) => [
                $role->label(),
                count($role->permissions()).' permissions',
            ])->toArray()
        );
    }
}
