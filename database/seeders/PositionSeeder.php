<?php

namespace Database\Seeders;

use App\Models\Election;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $elections = Election::all();

        foreach ($elections as $election) {
            $positions = $this->getPositionsForElection($election);

            foreach ($positions as $positionData) {
                Position::create([
                    'election_id' => $election->id,
                    'organization_id' => $election->organization_id,
                    'title' => $positionData['title'],
                    'description' => $positionData['description'],
                    'max_candidates' => $positionData['max_candidates'],
                    'amount_required' => $positionData['amount_required'] ?? 0,
                    'order' => $positionData['order'],
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Positions created successfully.');
    }

    private function getPositionsForElection(Election $election): array
    {
        $positions = [];

        switch ($election->title) {
            case 'Test Election - Student Government 2024':
                $positions = [
                    [
                        'title' => 'President',
                        'description' => 'Chief executive officer responsible for leading the student body and representing student interests.',
                        'max_candidates' => 4,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Vice President',
                        'description' => 'Second in command, supports the President and leads special initiatives.',
                        'max_candidates' => 3,
                        'order' => 2,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Secretary',
                        'description' => 'Maintains official records, minutes, and handles correspondence.',
                        'max_candidates' => 3,
                        'order' => 3,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Treasurer',
                        'description' => 'Manages financial affairs, budgets, and financial reporting.',
                        'max_candidates' => 2,
                        'order' => 4,
                        'amount_required' => 0,
                    ],
                ];
                break;

            case 'Student Government Elections 2024':
                $positions = [
                    [
                        'title' => 'Student Body President',
                        'description' => 'Chief executive officer of the student government, representing all students.',
                        'max_candidates' => 5,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Student Body Vice President',
                        'description' => 'Second-in-command, assists the president and oversees student committees.',
                        'max_candidates' => 5,
                        'order' => 2,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Student Senate (5 positions)',
                        'description' => 'Representatives who vote on student policies and budget allocations.',
                        'max_candidates' => 15,
                        'order' => 3,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Student Treasurer',
                        'description' => 'Manages student government finances and budget oversight.',
                        'max_candidates' => 4,
                        'order' => 4,
                        'amount_required' => 0,
                    ],
                ];
                break;

            case 'Homecoming Court Selection':
                $positions = [
                    [
                        'title' => 'Homecoming King',
                        'description' => 'Male representative for homecoming festivities.',
                        'max_candidates' => 8,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Homecoming Queen',
                        'description' => 'Female representative for homecoming festivities.',
                        'max_candidates' => 8,
                        'order' => 2,
                        'amount_required' => 0,
                    ],
                ];
                break;

            case 'Club Funding Allocation Vote':
                $positions = [
                    [
                        'title' => 'Priority Funding Recipients',
                        'description' => 'Student organizations to receive priority funding allocation.',
                        'max_candidates' => 20,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                ];
                break;

            case 'Board of Directors Election':
                $positions = [
                    [
                        'title' => 'Board Chair',
                        'description' => 'Leads board meetings and represents the association publicly.',
                        'max_candidates' => 4,
                        'order' => 1,
                        'amount_required' => 50.00,
                    ],
                    [
                        'title' => 'Board Members (3 positions)',
                        'description' => 'General board members who participate in governance decisions.',
                        'max_candidates' => 10,
                        'order' => 2,
                        'amount_required' => 25.00,
                    ],
                    [
                        'title' => 'Secretary/Treasurer',
                        'description' => 'Maintains records and oversees financial matters.',
                        'max_candidates' => 3,
                        'order' => 3,
                        'amount_required' => 35.00,
                    ],
                ];
                break;

            case 'Annual Conference Speaker Selection':
                $positions = [
                    [
                        'title' => 'Keynote Speaker',
                        'description' => 'Main speaker for the annual technology conference.',
                        'max_candidates' => 6,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Workshop Leaders (3 positions)',
                        'description' => 'Technical experts to lead specialized workshop sessions.',
                        'max_candidates' => 12,
                        'order' => 2,
                        'amount_required' => 0,
                    ],
                ];
                break;

            case 'League Commissioner Election':
                $positions = [
                    [
                        'title' => 'League Commissioner',
                        'description' => 'Oversees all league operations, schedules, and disputes.',
                        'max_candidates' => 3,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                ];
                break;

            case 'Team Captain Representatives':
                $positions = [
                    [
                        'title' => 'Captain Representatives (5 positions)',
                        'description' => 'Team captains who represent player interests in league decisions.',
                        'max_candidates' => 15,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                ];
                break;

            case 'Union Leadership Elections':
                $positions = [
                    [
                        'title' => 'Union President',
                        'description' => 'Chief representative of all union members.',
                        'max_candidates' => 3,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Union Vice President',
                        'description' => 'Assists president and leads specific committees.',
                        'max_candidates' => 4,
                        'order' => 2,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Secretary',
                        'description' => 'Maintains union records and communication.',
                        'max_candidates' => 3,
                        'order' => 3,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Treasurer',
                        'description' => 'Manages union finances and dues.',
                        'max_candidates' => 2,
                        'order' => 4,
                        'amount_required' => 0,
                    ],
                ];
                break;

            case 'Contract Negotiation Team':
                $positions = [
                    [
                        'title' => 'Lead Negotiator',
                        'description' => 'Primary representative in contract negotiations.',
                        'max_candidates' => 4,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                    [
                        'title' => 'Negotiation Team Members (3 positions)',
                        'description' => 'Support team for contract negotiations.',
                        'max_candidates' => 10,
                        'order' => 2,
                        'amount_required' => 0,
                    ],
                ];
                break;

            case 'Chamber President Election':
                $positions = [
                    [
                        'title' => 'Chamber President',
                        'description' => 'Leads the chamber and business advocacy efforts.',
                        'max_candidates' => 4,
                        'order' => 1,
                        'amount_required' => 75.00,
                    ],
                ];
                break;

            case 'Business of the Year Award':
                $positions = [
                    [
                        'title' => 'Business of the Year',
                        'description' => 'Outstanding business that exemplifies excellence and community service.',
                        'max_candidates' => 10,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                ];
                break;

            default:
                // Default positions for any unmatched elections
                $positions = [
                    [
                        'title' => 'General Position',
                        'description' => 'General election position.',
                        'max_candidates' => 5,
                        'order' => 1,
                        'amount_required' => 0,
                    ],
                ];
                break;
        }

        return $positions;
    }
}
