<?php
namespace Database\Seeders;
use App\Enums\PermissionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(PermissionType::cases() as $permission){
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
        $rolesWithPermissions = [
            'admin' => PermissionType::cases(),
            'employee' =>[],
        ];

        foreach ($rolesWithPermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();

            if (! $role){
                $this->command->warn("⚠️ Role '{$roleName}' not found. Skipping...");
                continue;
            }
            $role->givePermissionTo(array_map(fn($p) => $p->value ?? $p, $permissions));
        }
        $this->command->info('✅ Roles and permissions have been seeded successfully!');
    }


}
