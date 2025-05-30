<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SystemAdminSeeder::class,
            OrganizationSeeder::class,
            UserSeeder::class,
            ElectionSeeder::class,
            PositionSeeder::class,
            CandidateSeeder::class,
            VoterAccreditationSeeder::class,
            VoteSeeder::class,
        ]);
    }
}
