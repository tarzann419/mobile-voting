<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test organizations
        $organizations = [
            [
                'name' => 'University Student Union',
                'description' => 'Official student union for university-wide elections and governance.',
                'contact_email' => 'elections@university.edu',
                'contact_phone' => '+1-555-1001',
                'address' => '123 University Ave, College Town, ST 12345',
                'subscription_type' => 'premium',
                'admin_name' => 'John Anderson',
                'admin_email' => 'john.anderson@university.edu',
                'admin_phone' => '+1-555-1002',
            ],
            [
                'name' => 'Tech Professionals Association',
                'description' => 'Professional association for technology workers in the region.',
                'contact_email' => 'admin@techpro.org',
                'contact_phone' => '+1-555-2001',
                'address' => '456 Tech Boulevard, Innovation City, ST 54321',
                'subscription_type' => 'premium',
                'admin_name' => 'Maria Rodriguez',
                'admin_email' => 'maria.rodriguez@techpro.org',
                'admin_phone' => '+1-555-2002',
            ],
            [
                'name' => 'Community Sports League',
                'description' => 'Local sports league organizing elections for board positions.',
                'contact_email' => 'league@sportscomm.org',
                'contact_phone' => '+1-555-3001',
                'address' => '789 Sports Complex Dr, Athleticsville, ST 98765',
                'subscription_type' => 'basic',
                'admin_name' => 'David Kim',
                'admin_email' => 'david.kim@sportscomm.org',
                'admin_phone' => '+1-555-3002',
            ],
            [
                'name' => 'Healthcare Workers Union',
                'description' => 'Union representing healthcare professionals across multiple facilities.',
                'contact_email' => 'union@healthworkers.org',
                'contact_phone' => '+1-555-4001',
                'address' => '321 Medical Center Blvd, Healthtown, ST 13579',
                'subscription_type' => 'premium',
                'admin_name' => 'Dr. Emily Chen',
                'admin_email' => 'emily.chen@healthworkers.org',
                'admin_phone' => '+1-555-4002',
            ],
            [
                'name' => 'Local Business Chamber',
                'description' => 'Chamber of commerce for local business owners and entrepreneurs.',
                'contact_email' => 'chamber@localbiz.com',
                'contact_phone' => '+1-555-5001',
                'address' => '654 Commerce Street, Business District, ST 24680',
                'subscription_type' => 'premium',
                'admin_name' => 'Robert Thompson',
                'admin_email' => 'robert.thompson@localbiz.com',
                'admin_phone' => '+1-555-5002',
            ],
        ];

        foreach ($organizations as $orgData) {
            // Create organization
            $organization = Organization::firstOrCreate(
                ['contact_email' => $orgData['contact_email']],
                [
                    'name' => $orgData['name'],
                    'description' => $orgData['description'],
                    'slug' => Str::slug($orgData['name']),
                    'contact_email' => $orgData['contact_email'],
                    'contact_phone' => $orgData['contact_phone'],
                    'address' => $orgData['address'],
                    'is_active' => true,
                    'subscription_type' => $orgData['subscription_type'],
                    'subscription_expires_at' => now()->addYear(),
                ]
            );

            // Create organization admin
            User::firstOrCreate(
                ['email' => $orgData['admin_email']],
                [
                    'name' => $orgData['admin_name'],
                    'email' => $orgData['admin_email'],
                    'password' => Hash::make('password'),
                    'organization_id' => $organization->id,
                    'role' => 'organization_admin',
                    'phone' => $orgData['admin_phone'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('Organizations and their admins created successfully.');
    }
}
