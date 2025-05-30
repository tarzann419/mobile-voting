<?php

namespace App\Repositories;

use App\Models\Election;
use App\Repositories\Interfaces\ElectionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ElectionRepository implements ElectionRepositoryInterface
{
    public function findById(int $id): ?Election
    {
        return Election::find($id);
    }

    public function findByOrganization(int $organizationId): Collection
    {
        return Election::where('organization_id', $organizationId)->get();
    }

    public function findActiveElections(): Collection
    {
        return Election::where('status', 'active')
            ->where('voting_start_date', '<=', now())
            ->where('voting_end_date', '>=', now())
            ->get();
    }

    public function findByStatus(string $status): Collection
    {
        return Election::where('status', $status)->get();
    }

    public function create(array $data): Election
    {
        return Election::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Election::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Election::destroy($id);
    }

    public function paginate(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return Election::where('organization_id', $organizationId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getWithPositionsAndCandidates(int $id): ?Election
    {
        return Election::with([
            'positions.candidates.user',
            'positions.candidates.votes'
        ])->find($id);
    }

    public function getElectionResults(int $id): array
    {
        $election = $this->getWithPositionsAndCandidates($id);

        if (!$election) {
            return [];
        }

        $results = [];

        foreach ($election->positions as $position) {
            $positionResults = [
                'position' => $position,
                'candidates' => [],
                'total_votes' => 0
            ];

            foreach ($position->candidates as $candidate) {
                $voteCount = $candidate->votes->count();
                $positionResults['candidates'][] = [
                    'candidate' => $candidate,
                    'vote_count' => $voteCount,
                    'percentage' => 0 // Will be calculated after getting total
                ];
                $positionResults['total_votes'] += $voteCount;
            }

            // Calculate percentages
            foreach ($positionResults['candidates'] as &$candidateResult) {
                if ($positionResults['total_votes'] > 0) {
                    $candidateResult['percentage'] = round(
                        ($candidateResult['vote_count'] / $positionResults['total_votes']) * 100,
                        2
                    );
                }
            }

            // Sort by vote count
            usort($positionResults['candidates'], function ($a, $b) {
                return $b['vote_count'] <=> $a['vote_count'];
            });

            $results[] = $positionResults;
        }

        return $results;
    }

    public function getElectionsWithResults(int $organizationId): Collection
    {
        return Election::where('organization_id', $organizationId)
            ->where('voting_end_date', '<=', now()) // Only completed elections
            ->with(['positions.candidates.votes'])
            ->orderBy('voting_end_date', 'desc')
            ->get();
    }

    public function getElectionsForReports(int $organizationId): Collection
    {
        return Election::where('organization_id', $organizationId)
            ->whereIn('status', ['completed', 'active'])
            ->with(['positions.candidates.user', 'positions.candidates.votes'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
