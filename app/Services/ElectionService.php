<?php

namespace App\Services;

use App\Models\Election;
use App\Repositories\Interfaces\ElectionRepositoryInterface;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ElectionService
{
    public function __construct(
        private ElectionRepositoryInterface $electionRepository,
        private OrganizationRepositoryInterface $organizationRepository
    ) {}

    public function createElection(array $data): Election
    {
        return DB::transaction(function () use ($data) {
            $organization = $this->organizationRepository->findById($data['organization_id']);

            if (!$organization || !$organization->isActive()) {
                throw new \Exception('Organization is not active or does not exist.');
            }

            return $this->electionRepository->create($data);
        });
    }

    public function updateElection(int $id, array $data): Election
    {
        $election = $this->electionRepository->findById($id);

        if (!$election) {
            throw new \Exception('Election not found.');
        }

        // Validate status transitions
        if (isset($data['status'])) {
            $this->validateStatusTransition($election->status, $data['status']);
        }

        $this->electionRepository->update($id, $data);

        return $this->electionRepository->findById($id);
    }

    public function getElectionWithDetails(int $id): ?Election
    {
        return $this->electionRepository->getWithPositionsAndCandidates($id);
    }

    public function getElectionResults(int $id): array
    {
        return $this->electionRepository->getElectionResults($id);
    }

    public function publishElection(int $id): bool
    {
        $election = $this->electionRepository->findById($id);

        if (!$election) {
            throw new \Exception('Election not found.');
        }

        if ($election->status !== 'draft') {
            throw new \Exception('Only draft elections can be published.');
        }

        $this->validateElectionData($election);

        return $this->electionRepository->update($id, ['status' => 'published']);
    }

    public function startElection(int $id): bool
    {
        $election = $this->electionRepository->findById($id);

        if (!$election) {
            throw new \Exception('Election not found.');
        }

        if ($election->status !== 'published') {
            throw new \Exception('Only published elections can be started.');
        }

        if ($election->voting_start_date > now()) {
            throw new \Exception('Election start date has not been reached.');
        }

        return $this->electionRepository->update($id, ['status' => 'active']);
    }

    public function endElection(int $id): bool
    {
        $election = $this->electionRepository->findById($id);

        if (!$election) {
            throw new \Exception('Election not found.');
        }

        if ($election->status !== 'active') {
            throw new \Exception('Only active elections can be ended.');
        }

        return $this->electionRepository->update($id, ['status' => 'completed']);
    }

    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $allowedTransitions = [
            'draft' => ['published', 'cancelled'],
            'published' => ['active', 'cancelled'],
            'active' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => []
        ];

        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            throw new \Exception("Invalid status transition from {$currentStatus} to {$newStatus}.");
        }
    }

    public function deleteElection(int $id): bool
    {
        $election = $this->electionRepository->findById($id);

        if (!$election) {
            throw new \Exception('Election not found.');
        }

        // Only allow deletion of draft elections
        if ($election->status !== 'draft') {
            throw new \Exception('Only draft elections can be deleted.');
        }

        return $this->electionRepository->delete($id);
    }

    private function validateElectionData(Election $election): void
    {
        if ($election->positions->isEmpty()) {
            throw new \Exception('Election must have at least one position.');
        }

        foreach ($election->positions as $position) {
            if ($position->candidates->isEmpty()) {
                throw new \Exception("Position '{$position->title}' must have at least one candidate.");
            }
        }
    }

    public function getElectionStatistics(int $id): array
    {
        $election = $this->electionRepository->findById($id);

        if (!$election) {
            throw new \Exception('Election not found.');
        }

        $totalVotes = 0;
        $uniqueVoters = collect();

        // Count total votes and unique voters across all positions
        foreach ($election->positions as $position) {
            foreach ($position->votes as $vote) {
                $totalVotes++;
                $uniqueVoters->push($vote->user_id);
            }
        }

        $uniqueVoterCount = $uniqueVoters->unique()->count();
        $eligibleVoters = $election->organization->users()->where('role', 'voter')->count();
        $participationRate = $eligibleVoters > 0 ? round(($uniqueVoterCount / $eligibleVoters) * 100, 1) : 0;

        return [
            'total_votes' => $totalVotes,
            'unique_voters' => $uniqueVoterCount,
            'eligible_voters' => $eligibleVoters,
            'participation_rate' => $participationRate,
            'positions_count' => $election->positions->count(),
            'candidates_count' => $election->positions->sum(function ($position) {
                return $position->candidates->count();
            })
        ];
    }
}
