<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'route_name' => 'admin.dashboard'
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'route_name' => 'manager.dashboard'
            ]
        ];
        foreach ($modules as $module){
            Module::create($module);
        }
    }
}
