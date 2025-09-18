<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin account
        Admin::create([
            'adminName' => 'System Administrator',
            'username' => 'admin',
            'email' => 'admin@pasya.com',
            'password' => Hash::make('admin123'),
            'position' => 'System Administrator',
            'department' => 'IT Department',
            'is_active' => true,
        ]);

        // Create secondary admin for backup
        Admin::create([
            'adminName' => 'PASYA Admin',
            'username' => 'pasya_admin',
            'email' => 'pasya.admin@example.com',
            'password' => Hash::make('pasya2025'),
            'position' => 'Administrative Officer',
            'department' => 'Administration',
            'is_active' => true,
        ]);
    }
}