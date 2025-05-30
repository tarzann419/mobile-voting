<?php

namespace Database\Seeders;

use App\Models\Election;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class ElectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            // Create elections based on organization type
            $elections = $this->getElectionsForOrganization($organization);

            foreach ($elections as $electionData) {
                Election::create([
                    'organization_id' => $organization->id,
                    'title' => $electionData['title'],
                    'description' => $electionData['description'],
                    'status' => $electionData['status'],
                    'registration_start_date' => $electionData['registration_start_date'],
                    'registration_end_date' => $electionData['registration_end_date'],
                    'voting_start_date' => $electionData['voting_start_date'],
                    'voting_end_date' => $electionData['voting_end_date'],
                    'allow_multiple_votes' => $electionData['allow_multiple_votes'],
                    'require_payment' => $electionData['require_payment'],
                    'settings' => $electionData['settings'],
                ]);
            }
        }

        $this->command->info('Elections created successfully.');
    }

    private function getElectionsForOrganization(Organization $organization): array
    {
        $baseElections = [];

        switch ($organization->name) {
            case 'University Student Union':
                $baseElections = [
                    [
                        'title' => 'Test Election - Student Government 2024',
                        'description' => 'Test election for student government positions including President, Vice President, Secretary, and Treasurer for voting system testing.',
                        'status' => 'active',
                        'registration_start_date' => now()->subDays(10),
                        'registration_end_date' => now()->subDays(1),
                        'voting_start_date' => now(),
                        'voting_end_date' => now()->addDays(7),
                        'allow_multiple_votes' => false,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => true, 'show_results_after' => true],
                    ],
                    [
                        'title' => 'Student Government Elections 2024',
                        'description' => 'Annual elections for student government positions including President, Vice President, and Senate representatives.',
                        'status' => 'published',
                        'registration_start_date' => now()->subDays(30),
                        'registration_end_date' => now()->addDays(5),
                        'voting_start_date' => now()->addDays(7),
                        'voting_end_date' => now()->addDays(14),
                        'allow_multiple_votes' => false,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => true, 'show_results_after' => true],
                    ],
                    [
                        'title' => 'Homecoming Court Selection',
                        'description' => 'Selection of Homecoming King and Queen for the annual homecoming celebration.',
                        'status' => 'completed',
                        'registration_start_date' => now()->subDays(60),
                        'registration_end_date' => now()->subDays(45),
                        'voting_start_date' => now()->subDays(40),
                        'voting_end_date' => now()->subDays(35),
                        'allow_multiple_votes' => false,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => true, 'show_results_after' => true],
                    ],
                    [
                        'title' => 'Club Funding Allocation Vote',
                        'description' => 'Student vote on how to allocate activity fees among registered student organizations.',
                        'status' => 'draft',
                        'registration_start_date' => now()->addDays(30),
                        'registration_end_date' => now()->addDays(45),
                        'voting_start_date' => now()->addDays(50),
                        'voting_end_date' => now()->addDays(57),
                        'allow_multiple_votes' => true,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => true, 'show_results_after' => false],
                    ],
                ];
                break;

            case 'Tech Professionals Association':
                $baseElections = [
                    [
                        'title' => 'Board of Directors Election',
                        'description' => 'Annual election for the Board of Directors to guide association policies and initiatives.',
                        'status' => 'published',
                        'registration_start_date' => now()->subDays(20),
                        'registration_end_date' => now()->addDays(10),
                        'voting_start_date' => now()->addDays(12),
                        'voting_end_date' => now()->addDays(19),
                        'allow_multiple_votes' => false,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => true, 'show_results_after' => true],
                    ],
                    [
                        'title' => 'Annual Conference Speaker Selection',
                        'description' => 'Members vote on keynote speakers for the annual tech conference.',
                        'status' => 'active',
                        'registration_start_date' => now()->subDays(10),
                        'registration_end_date' => now()->subDays(2),
                        'voting_start_date' => now()->subDays(1),
                        'voting_end_date' => now()->addDays(6),
                        'allow_multiple_votes' => true,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => true, 'show_results_after' => false],
                    ],
                ];
                break;

            case 'Community Sports League':
                $baseElections = [
                    [
                        'title' => 'League Commissioner Election',
                        'description' => 'Election for the new League Commissioner to oversee all league operations.',
                        'status' => 'published',
                        'registration_start_date' => now()->subDays(15),
                        'registration_end_date' => now()->addDays(8),
                        'voting_start_date' => now()->addDays(10),
                        'voting_end_date' => now()->addDays(17),
                        'allow_multiple_votes' => false,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => true, 'show_results_after' => true],
                    ],
                    [
                        'title' => 'Team Captain Representatives',
                        'description' => 'Selection of team captains to represent player interests in league decisions.',
                        'status' => 'draft',
                        'registration_start_date' => now()->addDays(20),
                        'registration_end_date' => now()->addDays(35),
                        'voting_start_date' => now()->addDays(40),
                        'voting_end_date' => now()->addDays(47),
                        'allow_multiple_votes' => false,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => false, 'show_results_after' => true],
                    ],
                ];
                break;

            case 'Healthcare Workers Union':
                $baseElections = [
                    [
                        'title' => 'Union Leadership Elections',
                        'description' => 'Elections for President, Vice President, Secretary, and Treasurer positions.',
                        'status' => 'active',
                        'registration_start_date' => now()->subDays(25),
                        'registration_end_date' => now()->subDays(3),
                        'voting_start_date' => now()->subDays(1),
                        'voting_end_date' => now()->addDays(5),
                        'allow_multiple_votes' => false,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => true, 'show_results_after' => true],
                    ],
                    [
                        'title' => 'Contract Negotiation Team',
                        'description' => 'Selection of representatives for upcoming contract negotiations with hospital administration.',
                        'status' => 'completed',
                        'registration_start_date' => now()->subDays(90),
                        'registration_end_date' => now()->subDays(75),
                        'voting_start_date' => now()->subDays(70),
                        'voting_end_date' => now()->subDays(65),
                        'allow_multiple_votes' => true,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => true, 'show_results_after' => true],
                    ],
                ];
                break;

            case 'Local Business Chamber':
                $baseElections = [
                    [
                        'title' => 'Chamber President Election',
                        'description' => 'Annual election for Chamber President to lead business advocacy efforts.',
                        'status' => 'published',
                        'registration_start_date' => now()->subDays(18),
                        'registration_end_date' => now()->addDays(7),
                        'voting_start_date' => now()->addDays(9),
                        'voting_end_date' => now()->addDays(16),
                        'allow_multiple_votes' => false,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => true, 'show_results_after' => true],
                    ],
                    [
                        'title' => 'Business of the Year Award',
                        'description' => 'Members vote for the outstanding business of the year award recipient.',
                        'status' => 'draft',
                        'registration_start_date' => now()->addDays(60),
                        'registration_end_date' => now()->addDays(75),
                        'voting_start_date' => now()->addDays(80),
                        'voting_end_date' => now()->addDays(87),
                        'allow_multiple_votes' => false,
                        'require_payment' => false,
                        'settings' => ['anonymous_voting' => false, 'show_results_after' => true],
                    ],
                ];
                break;
        }

        return $baseElections;
    }
}
