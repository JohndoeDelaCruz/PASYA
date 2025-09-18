<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'farmer',
                'description' => 'Farmer role with access to farming dashboard and tools'
            ],
            [
                'name' => 'admin',
                'description' => 'Administrator role with full system access'
            ],
            [
                'name' => 'researcher',
                'description' => 'Researcher role with access to data analysis tools'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
