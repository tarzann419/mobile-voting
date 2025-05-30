<?php

namespace Database\Seeders;

use App\Models\Election;
use App\Models\User;
use App\Models\VoterAccreditation;
use Illuminate\Database\Seeder;

class VoterAccreditationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $elections = Election::with('organization')->get();

        foreach ($elections as $election) {
            // Get all voters from the organization
            $voters = User::where('organization_id', $election->organization_id)
                ->where('role', 'voter')
                ->where('is_active', true)
                ->get();

            foreach ($voters as $voter) {
                // Create voter accreditation based on election status
                $accreditationData = $this->getAccreditationData($election, $voter);

                if ($accreditationData) {
                    VoterAccreditation::create($accreditationData);
                }
            }
        }

        $this->command->info('Voter accreditations created successfully.');
    }

    private function getAccreditationData(Election $election, User $voter): ?array
    {
        // Determine if this voter should be accredited based on election status
        $shouldAccredit = $this->shouldAccreditVoter($election);

        if (!$shouldAccredit) {
            return null;
        }

        $status = $this->getAccreditationStatus($election);
        $registrationDate = $this->getRegistrationDate($election);

        return [
            'user_id' => $voter->id,
            'election_id' => $election->id,
            'organization_id' => $election->organization_id,
            'status' => $status,
            'applied_at' => $registrationDate,
            'reviewed_at' => $status !== 'pending' ? $registrationDate->copy()->addHours(rand(1, 24)) : null,
            'reviewed_by' => $status !== 'pending' ? $this->getReviewer($election) : null,
            'documents' => $this->getDocuments(),
            'verification_notes' => $status === 'rejected' ? $this->getRejectionReason() : null,
        ];
    }

    private function shouldAccreditVoter(Election $election): bool
    {
        switch ($election->status) {
            case 'draft':
                return rand(1, 100) <= 10; // 10% chance for draft elections
            case 'published':
                return rand(1, 100) <= 85; // 85% chance for published elections
            case 'active':
                return rand(1, 100) <= 95; // 95% chance for active elections
            case 'completed':
                return rand(1, 100) <= 98; // 98% chance for completed elections
            default:
                return false;
        }
    }

    private function getAccreditationStatus(Election $election): string
    {
        switch ($election->status) {
            case 'draft':
                $statuses = ['pending' => 80, 'approved' => 15, 'rejected' => 5];
                break;
            case 'published':
                $statuses = ['approved' => 85, 'pending' => 10, 'rejected' => 5];
                break;
            case 'active':
                $statuses = ['approved' => 95, 'pending' => 3, 'rejected' => 2];
                break;
            case 'completed':
                $statuses = ['approved' => 98, 'rejected' => 2];
                break;
            default:
                $statuses = ['pending' => 100];
        }

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($statuses as $status => $percentage) {
            $cumulative += $percentage;
            if ($random <= $cumulative) {
                return $status;
            }
        }

        return 'pending';
    }

    private function getRegistrationDate(Election $election): \Carbon\Carbon
    {
        $start = $election->registration_start_date;
        $end = $election->registration_end_date;

        // Most registrations happen in the first 70% of the registration period
        $totalDays = $start->diffInDays($end);
        $activeRegistrationDays = (int) ($totalDays * 0.7);

        $randomDays = rand(0, max(1, $activeRegistrationDays));
        return $start->copy()->addDays($randomDays)->addHours(rand(0, 23))->addMinutes(rand(0, 59));
    }

    private function getReviewer(Election $election): ?int
    {
        // Get a random organization admin to approve the accreditation
        $orgAdmin = User::where('organization_id', $election->organization_id)
            ->where('role', 'organization_admin')
            ->inRandomOrder()
            ->first();

        return $orgAdmin ? $orgAdmin->id : null;
    }

    private function getDocuments(): array
    {
        // Generate some sample document paths
        $documentTypes = [
            'id_document.pdf',
            'membership_certificate.pdf',
            'proof_of_address.pdf',
            'student_id.jpg',
            'employment_letter.pdf',
        ];

        // Return 1-3 random documents
        $numDocs = rand(1, 3);
        $selectedDocs = array_rand($documentTypes, $numDocs);

        if (is_int($selectedDocs)) {
            $selectedDocs = [$selectedDocs];
        }

        $documents = [];
        foreach ($selectedDocs as $index) {
            $documents[] = [
                'name' => $documentTypes[$index],
                'path' => 'documents/' . uniqid() . '_' . $documentTypes[$index],
                'size' => rand(50000, 2000000), // 50KB to 2MB
                'uploaded_at' => now()->toISOString(),
            ];
        }

        return $documents;
    }

    private function getRejectionReason(): string
    {
        $reasons = [
            'Incomplete documentation provided',
            'Email verification failed',
            'Not eligible for this election',
            'Duplicate registration detected',
            'Invalid membership status',
            'Missing required information',
        ];

        return $reasons[array_rand($reasons)];
    }
}
