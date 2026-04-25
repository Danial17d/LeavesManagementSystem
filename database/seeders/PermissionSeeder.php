<?php

namespace Database\Seeders;

use App\Enums\PermissionType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionType::cases() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission->value,
                'guard_name' => 'web',
            ]);
        }

        $rolesWithPermissions = [
            'Super Admin'=> PermissionType::cases(),
            'Admin' => [
                PermissionType::UserList,
                PermissionType::UserView,
                PermissionType::UserCreate,
                PermissionType::UserEdit,
                PermissionType::StructureList,
                PermissionType::StructureView,
                PermissionType::StructureAssign,
                PermissionType::StructureMove,
                PermissionType::LeaveApprovalList,
                PermissionType::LeaveApprovalView,
                PermissionType::LeaveApprovalEdit,
                PermissionType::LeaveTypeList,
                PermissionType::LeaveTypeView,
                PermissionType::LeaveTypeCreate,
                PermissionType::LeaveTypeEdit,
                PermissionType::CalendarView,
            ],
            'Employee' => [
                PermissionType::LeaveRequestList,
                PermissionType::LeaveRequestView,
                PermissionType::LeaveRequestCreate,
                PermissionType::LeaveRequestEdit,
                PermissionType::LeaveRequestDelete,
                PermissionType::CalendarView,
                PermissionType::StructureRequestList,
                PermissionType::StructureRequestView,
                PermissionType::StructureRequestCreate,
                PermissionType::StructureRequestEdit,
                PermissionType::StructureRequestDelete,
            ],
        ];

        foreach ($rolesWithPermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();

            if (! $role) {
                $this->command->warn("Role '{$roleName}' not found. Skipping.");
                continue;
            }

            $role->syncPermissions(array_map(fn ($permission) => $permission->value ?? $permission, $permissions));
        }

        $this->command->info('Roles and permissions have been seeded successfully.');
    }
}
