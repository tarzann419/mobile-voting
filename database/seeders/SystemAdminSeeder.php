<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system admin
        User::firstOrCreate(
            ['email' => 'admin@voteapp.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@voteapp.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'organization_id' => null,
                'phone' => '+1-555-0001',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create additional system admins for testing
        User::firstOrCreate(
            ['email' => 'admin2@voteapp.com'],
            [
                'name' => 'Sarah Johnson',
                'email' => 'admin2@voteapp.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'organization_id' => null,
                'phone' => '+1-555-0002',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('System administrators created successfully.');
    }
}
