<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@davaocity.gov.ph',
            'password' => 'admin123', // Will be auto-hashed by model cast
        ]);

        // You can add more admins here if needed
        // Admin::create([
        //     'name' => 'Super Admin',
        //     'email' => 'superadmin@davaocity.gov.ph',
        //     'password' => 'superadmin123',
        // ]);
    }
}
