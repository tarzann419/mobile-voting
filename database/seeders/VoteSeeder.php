<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use App\Models\Vote;
use App\Models\VoterAccreditation;
use Illuminate\Database\Seeder;

class VoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create votes for active and completed elections
        $elections = Election::whereIn('status', ['active', 'completed'])->get();

        foreach ($elections as $election) {
            // Get approved voters for this election
            $approvedVoters = VoterAccreditation::where('election_id', $election->id)
                ->where('status', 'approved')
                ->get();

            // Determine voting participation rate based on election status
            $participationRate = $election->status === 'completed' ? rand(65, 85) : rand(30, 60);
            $votersToVote = $approvedVoters->random((int) ($approvedVoters->count() * $participationRate / 100));

            foreach ($votersToVote as $voterAccreditation) {
                $this->createVotesForElection($election, $voterAccreditation->user_id);
            }
        }

        $this->command->info('Votes created successfully.');
    }

    private function createVotesForElection(Election $election, int $userId): void
    {
        $positions = Position::where('election_id', $election->id)->get();

        foreach ($positions as $position) {
            // Skip some positions randomly (not all voters vote for all positions)
            if (rand(1, 100) <= 10) { // 10% chance to skip a position
                continue;
            }

            $candidates = Candidate::where('position_id', $position->id)
                ->where('status', 'approved')
                ->get();

            if ($candidates->isEmpty()) {
                continue;
            }

            $this->createVoteForPosition($election, $position, $candidates, $userId);
        }
    }

    private function createVoteForPosition(Election $election, Position $position, $candidates, int $userId): void
    {
        // The current table structure only supports single choice votes
        $this->createSingleChoiceVote($election, $position, $candidates, $userId);
    }

    private function createSingleChoiceVote(Election $election, Position $position, $candidates, int $userId): void
    {
        $selectedCandidate = $candidates->random();

        Vote::create([
            'user_id' => $userId,
            'organization_id' => $election->organization_id,
            'election_id' => $election->id,
            'position_id' => $position->id,
            'candidate_id' => $selectedCandidate->id,
            'vote_hash' => hash('sha256', $userId . $election->id . $position->id . $selectedCandidate->id . now()->timestamp),
            'voted_at' => $this->getVotingTime($election),
            'ip_address' => $this->generateRandomIP(),
            'user_agent' => $this->getRandomUserAgent(),
        ]);
    }

    private function getVotingTime(Election $election): \Carbon\Carbon
    {
        $start = $election->voting_start_date;
        $end = $election->voting_end_date;

        if ($election->status === 'completed') {
            // For completed elections, votes are spread throughout the voting period
            $totalMinutes = $start->diffInMinutes($end);
            $randomMinutes = rand(0, $totalMinutes);
            return $start->copy()->addMinutes($randomMinutes);
        } else {
            // For active elections, votes are between start and now
            $now = now();
            $endTime = $now->lt($end) ? $now : $end;
            $totalMinutes = $start->diffInMinutes($endTime);
            $randomMinutes = rand(0, max(1, $totalMinutes));
            return $start->copy()->addMinutes($randomMinutes);
        }
    }

    private function generateRandomIP(): string
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 255);
    }

    private function getRandomUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0 Firefox/88.0',
        ];

        return $userAgents[array_rand($userAgents)];
    }
}
