<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update existing admin role
DB::table('admins')->where('email', 'admin@davaocity.gov.ph')->update(['role' => 'admin']);

// Create super admin if not exists
try {
    DB::table('admins')->insert([
        'name' => 'Super Admin',
        'email' => 'superadmin@davaocity.gov.ph',
        'password' => bcrypt('superadmin123'),
        'role' => 'super_admin',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "✓ Super Admin created successfully!\n";
} catch (Exception $e) {
    echo "✗ Super admin might already exist or error: " . $e->getMessage() . "\n";
}

echo "✓ Admin roles updated successfully!\n";
echo "\nLogin credentials:\n";
echo "Super Admin: superadmin@davaocity.gov.ph / superadmin123\n";
echo "Admin: admin@davaocity.gov.ph / admin123\n";
