<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $permissions = [
            'dashboard_view',
            'user_view',
            'user_create',
            'user_edit',
            'user_delete',
            'role_view',
            'role_create',
            'role_edit',
            'role_delete',
            'recycle_bin_user_view',
            'recycle_bin_user_restore',
            'recycle_bin_user_permanent_delete',
            'application_configuration',
            'profile_view',
            'profile_edit'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
