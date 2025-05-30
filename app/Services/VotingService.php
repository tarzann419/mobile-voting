<?php

namespace App\Services;

use App\Models\Vote;
use App\Models\Election;
use App\Models\Candidate;
use App\Repositories\Interfaces\VoteRepositoryInterface;
use App\Repositories\Interfaces\ElectionRepositoryInterface;
use App\Repositories\Interfaces\VoterAccreditationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class VotingService
{
    public function __construct(
        private VoteRepositoryInterface $voteRepository,
        private ElectionRepositoryInterface $electionRepository,
        private VoterAccreditationRepositoryInterface $voterAccreditationRepository
    ) {}

    public function castVote(int $userId, int $candidateId, ?string $ipAddress = null, ?string $userAgent = null): Vote
    {
        return DB::transaction(function () use ($userId, $candidateId, $ipAddress, $userAgent) {
            $candidate = Candidate::with(['position.election'])->find($candidateId);

            if (!$candidate) {
                throw new \Exception('Candidate not found.');
            }

            $election = $candidate->position->election;
            $position = $candidate->position;

            // Validate election is active
            if (!$election->isVotingActive()) {
                throw new \Exception('Voting is not currently active for this election.');
            }

            // Check if user is accredited
            if (!$this->voterAccreditationRepository->isUserAccredited($userId, $election->id)) {
                throw new \Exception('User is not accredited to vote in this election.');
            }

            // Check if user has already voted for this position
            if ($this->voteRepository->hasUserVotedForPosition($userId, $position->id)) {
                if (!$election->allow_multiple_votes) {
                    throw new \Exception('User has already voted for this position.');
                }
            }

            // Check if candidate is eligible
            if (!$candidate->isEligible()) {
                throw new \Exception('Candidate is not eligible to receive votes.');
            }

            return $this->voteRepository->create([
                'organization_id' => $election->organization_id,
                'election_id' => $election->id,
                'position_id' => $position->id,
                'candidate_id' => $candidate->id,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        });
    }

    public function getRealTimeResults(int $electionId): array
    {
        $election = $this->electionRepository->findById($electionId);

        if (!$election) {
            throw new \Exception('Election not found.');
        }

        return $this->voteRepository->getRealTimeResults($electionId);
    }

    public function getUserVotingHistory(int $userId, ?int $electionId = null): array
    {
        $query = Vote::where('user_id', $userId);

        if ($electionId) {
            $query = $query->where('election_id', $electionId);
        }

        return $query->with(['election', 'position', 'candidate.user'])
            ->orderBy('voted_at', 'desc')
            ->get()
            ->toArray();
    }

    public function canUserVote(int $userId, int $electionId): array
    {
        $election = $this->electionRepository->findById($electionId);

        if (!$election) {
            return ['can_vote' => false, 'reason' => 'Election not found.'];
        }

        if (!$election->isVotingActive()) {
            return ['can_vote' => false, 'reason' => 'Voting is not currently active.'];
        }

        if (!$this->voterAccreditationRepository->isUserAccredited($userId, $electionId)) {
            return ['can_vote' => false, 'reason' => 'User is not accredited to vote.'];
        }

        return ['can_vote' => true, 'reason' => ''];
    }

    public function getVotablePositions(int $userId, int $electionId): array
    {
        $canVote = $this->canUserVote($userId, $electionId);

        if (!$canVote['can_vote']) {
            return [];
        }

        $election = $this->electionRepository->getWithPositionsAndCandidates($electionId);
        $votablePositions = [];

        foreach ($election->positions as $position) {
            $hasVoted = $this->voteRepository->hasUserVotedForPosition($userId, $position->id);

            if (!$hasVoted || $election->allow_multiple_votes) {
                $eligibleCandidates = $position->candidates->filter(function ($candidate) {
                    return $candidate->isEligible();
                });

                if ($eligibleCandidates->count() > 0) {
                    $votablePositions[] = [
                        'position' => $position,
                        'candidates' => $eligibleCandidates,
                        'has_voted' => $hasVoted
                    ];
                }
            }
        }

        return $votablePositions;
    }

    public function getAvailablePositionsToVote(int $userId, int $electionId): array
    {
        return $this->getVotablePositions($userId, $electionId);
    }
}
