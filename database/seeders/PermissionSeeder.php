<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'admin_user-management_module-list', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_module-create', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_module-show', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_module-edit', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_module-delete', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_module-activity-log', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_module-activity-log-trash', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-group-list', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-group-create', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-group-show', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-group-edit', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-group-activity-log', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-group-activity-log-trash', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-group-delete', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-list', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-create', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-show', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-edit', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_permission-delete', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_role-list', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_role-create', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_role-show', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_role-edit', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_role-delete', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_user-list', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_user-create', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_user-show', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_user-edit', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_user-activity-log', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_user-activity-log-trash', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_user-delete', 'group_id' => 1, 'module_id'=>1],
            ['name' => 'admin_user-management_backup-list', 'group_id' => 2, 'module_id'=>1],
            ['name' => 'admin_user-management_backup-create', 'group_id' => 2, 'module_id'=>1],
            ['name' => 'admin_user-management_backup-download', 'group_id' => 2, 'module_id'=>1],
            ['name' => 'admin_user-management_backup-delete', 'group_id' => 2, 'module_id'=>1],
            ['name' => 'admin_user-management_log-dashboard', 'group_id' => 2, 'module_id'=>1],
            ['name' => 'admin_user-management_log-list', 'group_id' => 2, 'module_id'=>1],
            ['name' => 'admin_user-management_log-show', 'group_id' => 2, 'module_id'=>1],
            ['name' => 'admin_user-management_log-download', 'group_id' => 2, 'module_id'=>1],
            ['name' => 'admin_user-management_log-delete', 'group_id' => 2, 'module_id'=>1],
        ];
        foreach ($permissions as $permission){
            Permission::create($permission);
        }
    }
}
