<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $this->command->info("╔══ Seeding roles...");
        foreach (UserRole::cases() as $role) {
            $this->command->info("║ Seeding role:".$role->value);
            Role::firstOrCreate(['name' => $role->value
                ,'guard_name' => 'web']);
        }
        $this->command->info('╚══ Seeding roles... done');

    }
}
