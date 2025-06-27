<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $user = User::create([
            'employee_id' => "EM_001",
            'name' => "Administrator",
            'gender' => "Male",
            'date_of_birth' => "2000-01-01",
            'mobile_number' => '01XXXXXXXXX',
            'email' => "admin@example.com",
            'password' => bcrypt('123456'),
            'position' => "Administrator",
            'status' => "Active",
        ]);

        $role = Role::create([
            'name' => 'Super Admin',
            'description' => 'A Super Administrator is a user who has complete access to all objects, folders, role templates, and groups in the system.'
        ]);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
