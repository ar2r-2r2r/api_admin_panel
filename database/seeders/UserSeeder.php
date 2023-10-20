<?php

namespace Database\Seeders;

use App\Models\ManagerEmployee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $managersCount = 3;
        $employeesCount = 5;
        $role = Role::firstOrNew(['name' => 'manager']);
        User::factory()->count($managersCount)->create([
            'role_id' => $role->id,
        ]);
        $role = Role::firstOrNew(['name' => 'employee']);
        User::factory()->count($employeesCount)->create([
            'role_id' => $role->id,
        ]);
        for ($i = 1; $i <= $employeesCount; $i++) {
            ManagerEmployee::create([
                'manager_id' => random_int(1, $managersCount),
                'employee_id' => $i+$managersCount,
            ]);
        }



    }
}
