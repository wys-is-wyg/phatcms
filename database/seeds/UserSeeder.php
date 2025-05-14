<?php

use App\Models\User;

class UserSeeder
{
    /**
     * Run the seeder
     */
    public function run()
    {
        // Create admin user
        $admin = new User([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password'
        ]);
        
        $admin->save();
        
        echo "Admin user created with email: admin@example.com and password: password\n";
    }
} 