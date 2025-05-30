<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            // Create 15-20 voters per organization
            $voterCount = rand(15, 20);

            for ($i = 1; $i <= $voterCount; $i++) {
                $firstName = $this->getRandomFirstName();
                $lastName = $this->getRandomLastName();
                $email = strtolower($firstName . '.' . $lastName . '.' . $i . '@' . $this->getEmailDomain($organization->name));

                User::create([
                    'name' => $firstName . ' ' . $lastName,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'organization_id' => $organization->id,
                    'role' => 'voter',
                    'phone' => '+1-555-' . rand(1000, 9999),
                    'is_active' => rand(0, 10) > 1, // 90% active users
                    'email_verified_at' => rand(0, 10) > 2 ? now() : null, // 80% verified
                ]);
            }

            // Create 2-3 additional organization admins per organization
            $adminCount = rand(2, 3);
            for ($i = 1; $i <= $adminCount; $i++) {
                $firstName = $this->getRandomFirstName();
                $lastName = $this->getRandomLastName();
                $email = 'admin.' . strtolower($firstName . '.' . $lastName) . '@' . $this->getEmailDomain($organization->name);

                User::create([
                    'name' => $firstName . ' ' . $lastName,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'organization_id' => $organization->id,
                    'role' => 'organization_admin',
                    'phone' => '+1-555-' . rand(1000, 9999),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            }
        }

        $this->command->info('Voters and additional organization admins created successfully.');
    }

    private function getRandomFirstName(): string
    {
        $names = [
            'James',
            'Mary',
            'John',
            'Patricia',
            'Robert',
            'Jennifer',
            'Michael',
            'Linda',
            'William',
            'Elizabeth',
            'David',
            'Barbara',
            'Richard',
            'Susan',
            'Joseph',
            'Jessica',
            'Thomas',
            'Sarah',
            'Christopher',
            'Karen',
            'Charles',
            'Nancy',
            'Daniel',
            'Lisa',
            'Matthew',
            'Betty',
            'Anthony',
            'Helen',
            'Mark',
            'Sandra',
            'Donald',
            'Donna',
            'Steven',
            'Carol',
            'Paul',
            'Ruth',
            'Andrew',
            'Sharon',
            'Joshua',
            'Michelle',
            'Kenneth',
            'Laura',
            'Kevin',
            'Sarah',
            'Brian',
            'Kimberly',
            'George',
            'Deborah',
            'Timothy',
            'Dorothy',
            'Ronald',
            'Lisa',
            'Jason',
            'Nancy',
            'Edward',
            'Karen',
            'Jeffrey',
            'Betty',
            'Ryan',
            'Helen',
            'Jacob',
            'Sandra',
            'Gary',
            'Donna',
        ];

        return $names[array_rand($names)];
    }

    private function getRandomLastName(): string
    {
        $names = [
            'Smith',
            'Johnson',
            'Williams',
            'Brown',
            'Jones',
            'Garcia',
            'Miller',
            'Davis',
            'Rodriguez',
            'Martinez',
            'Hernandez',
            'Lopez',
            'Gonzalez',
            'Wilson',
            'Anderson',
            'Thomas',
            'Taylor',
            'Moore',
            'Jackson',
            'Martin',
            'Lee',
            'Perez',
            'Thompson',
            'White',
            'Harris',
            'Sanchez',
            'Clark',
            'Ramirez',
            'Lewis',
            'Robinson',
            'Walker',
            'Young',
            'Allen',
            'King',
            'Wright',
            'Scott',
            'Torres',
            'Nguyen',
            'Hill',
            'Flores',
            'Green',
            'Adams',
            'Nelson',
            'Baker',
            'Hall',
            'Rivera',
            'Campbell',
            'Mitchell',
            'Carter',
            'Roberts',
            'Gomez',
            'Phillips',
            'Evans',
            'Turner',
            'Diaz',
        ];

        return $names[array_rand($names)];
    }

    private function getEmailDomain(string $organizationName): string
    {
        $domains = [
            'University Student Union' => 'university.edu',
            'Tech Professionals Association' => 'techpro.org',
            'Community Sports League' => 'sportscomm.org',
            'Healthcare Workers Union' => 'healthworkers.org',
            'Local Business Chamber' => 'localbiz.com',
        ];

        return $domains[$organizationName] ?? 'example.com';
    }
}
