<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = Position::with(['election.organization', 'election'])->get();

        foreach ($positions as $position) {
            // Get users from the same organization who can be candidates
            $organizationUsers = User::where('organization_id', $position->organization_id)
                ->where('role', 'voter')
                ->where('is_active', true)
                ->get();

            if ($organizationUsers->isEmpty()) {
                continue;
            }

            // Create candidates for this position (usually 60-80% of max_candidates)
            $candidateCount = max(1, (int) ($position->max_candidates * rand(60, 80) / 100));
            $selectedUsers = $organizationUsers->random(min($candidateCount, $organizationUsers->count()));

            foreach ($selectedUsers as $user) {
                $status = $this->getCandidateStatus($position->election->status);
                $registrationDate = $this->getRegistrationDate($position->election);

                Candidate::create([
                    'user_id' => $user->id,
                    'position_id' => $position->id,
                    'organization_id' => $position->organization_id,
                    'manifesto' => $this->generateManifesto($user->name, $position->title),
                    'photo' => $this->generatePhotoUrl($user->name),
                    'status' => $status,
                    'payment_confirmed' => $this->getPaymentStatus($position->election->status),
                    'registered_at' => $registrationDate,
                    'approved_at' => $status === 'approved' ? $registrationDate->copy()->addHours(rand(2, 48)) : null,
                ]);
            }
        }

        $this->command->info('Candidates created successfully.');
    }

    private function generateManifesto(string $name, string $positionTitle): string
    {
        $biography = $this->generateBiography($name, $positionTitle);
        $platform = $this->generatePlatformStatement($positionTitle);

        return $biography . "\n\n" . $platform;
    }

    private function generateBiography(string $name, string $positionTitle): string
    {
        $templates = [
            "Hello! I'm {name}, and I'm excited to run for {position}. I have been an active member of our community for several years, participating in various initiatives and committees. My experience in leadership roles has prepared me to take on this responsibility and serve our organization effectively.",

            "{name} here! As a dedicated member with a passion for positive change, I believe I can bring fresh perspectives and innovative solutions to the role of {position}. My background in community service and collaborative leadership makes me well-suited for this position.",

            "My name is {name}, and I'm committed to representing the best interests of our members as {position}. With experience in project management and team coordination, I understand the importance of transparent communication and inclusive decision-making.",

            "I'm {name}, and I've been actively involved in our organization's activities for years. Running for {position} represents my commitment to giving back to the community that has given me so much. I believe in working together to achieve our common goals.",

            "As {name}, I bring a unique combination of analytical thinking and people skills to the {position} role. My experience in both professional and volunteer settings has taught me the value of listening to diverse perspectives and finding common ground.",
        ];

        $template = $templates[array_rand($templates)];
        return str_replace(['{name}', '{position}'], [$name, $positionTitle], $template);
    }

    private function generatePlatformStatement(string $positionTitle): string
    {
        $statements = [
            'Transparency and Accountability' => 'I pledge to maintain open communication with all members, provide regular updates on decisions and initiatives, and ensure that every voice is heard in our democratic process.',

            'Innovation and Progress' => 'Our organization needs fresh ideas and modern approaches. I will work to implement new technologies and streamlined processes while respecting our valuable traditions.',

            'Community Building' => 'Strengthening our community bonds is my top priority. I plan to organize more inclusive events, improve member engagement, and create opportunities for meaningful connections.',

            'Financial Responsibility' => 'I am committed to responsible budget management, seeking cost-effective solutions, and ensuring maximum value for member dues and fees.',

            'Member Empowerment' => 'Every member should have the opportunity to contribute and grow. I will create mentorship programs, expand leadership development, and ensure equal opportunities for all.',

            'Effective Communication' => 'Clear, timely communication is essential. I will establish regular newsletters, open forums, and multiple channels for members to stay informed and engaged.',

            'Collaborative Leadership' => 'The best decisions come from working together. I believe in building consensus, respecting different viewpoints, and creating inclusive decision-making processes.',

            'Strategic Growth' => 'Our organization has great potential for positive growth. I will focus on strategic partnerships, membership expansion, and sustainable development initiatives.',
        ];

        $statementKeys = array_keys($statements);
        $numPoints = rand(2, 4);
        $selectedIndices = array_rand($statementKeys, $numPoints);

        // Ensure $selectedIndices is always an array
        if (!is_array($selectedIndices)) {
            $selectedIndices = [$selectedIndices];
        }

        $platform = [];

        foreach ($selectedIndices as $index) {
            $title = $statementKeys[$index];
            $description = $statements[$title];
            $platform[] = "**{$title}**: {$description}";
        }

        return implode("\n\n", $platform);
    }

    private function generatePhotoUrl(string $name): ?string
    {
        // Generate placeholder photo URLs using a service like UI Avatars
        $initials = collect(explode(' ', $name))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');

        $backgrounds = ['007bff', '28a745', 'dc3545', 'ffc107', '17a2b8', '6f42c1', 'fd7e14', '20c997'];
        $background = $backgrounds[array_rand($backgrounds)];

        return "https://ui-avatars.com/api/?name={$initials}&size=200&background={$background}&color=ffffff&bold=true";
    }

    private function getPaymentStatus(string $electionStatus): bool
    {
        switch ($electionStatus) {
            case 'draft':
                return rand(0, 10) > 7; // 30% paid
            case 'published':
                return rand(0, 10) > 3; // 70% paid
            case 'active':
            case 'completed':
                return rand(0, 10) > 1; // 90% paid
            default:
                return false;
        }
    }

    private function getCandidateStatus(string $electionStatus): string
    {
        switch ($electionStatus) {
            case 'draft':
                return 'pending';
            case 'published':
                return rand(0, 10) > 2 ? 'approved' : 'pending'; // 80% approved
            case 'active':
                return 'approved';
            case 'completed':
                return 'approved';
            default:
                return 'pending';
        }
    }

    private function getRegistrationDate($election): \Carbon\Carbon
    {
        // Random date between registration start and a few days before registration end
        $start = $election->registration_start_date;
        $end = $election->registration_end_date->copy()->subDays(rand(1, 3));

        $daysDiff = $start->diffInDays($end);
        $randomDays = rand(0, max(1, $daysDiff));

        return $start->copy()->addDays($randomDays);
    }
}
