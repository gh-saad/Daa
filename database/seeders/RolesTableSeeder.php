<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['id' => 1, 'name' => 'super admin', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 2, 'name' => 'company', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 3, 'name' => 'client', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 4, 'name' => 'staff', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 5, 'name' => 'vendor', 'guard_name' => 'web', 'module' => 'Account', 'created_by' => 0],
            ['id' => 6, 'name' => 'role6', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 7, 'name' => 'role7', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 8, 'name' => 'client', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 9, 'name' => 'role9', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 10, 'name' => 'role10', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 11, 'name' => 'role11', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 12, 'name' => 'role12', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 13, 'name' => 'role13', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 14, 'name' => 'role14', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 15, 'name' => 'role15', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 16, 'name' => 'role16', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 17, 'name' => 'role17', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 18, 'name' => 'role18', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 19, 'name' => 'role19', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 20, 'name' => 'role20', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 21, 'name' => 'role21', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 22, 'name' => 'role22', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 23, 'name' => 'role23', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 24, 'name' => 'role24', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
            ['id' => 25, 'name' => 'role25', 'guard_name' => 'web', 'module' => 'Base', 'created_by' => 0],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }
    }
}
