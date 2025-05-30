<?php

namespace App\Repositories;

use App\Models\Candidate;
use App\Repositories\Interfaces\CandidateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CandidateRepository implements CandidateRepositoryInterface
{
    public function findById(int $id): ?Candidate
    {
        return Candidate::find($id);
    }

    public function findByPosition(int $positionId): Collection
    {
        return Candidate::where('position_id', $positionId)->get();
    }

    public function findByUser(int $userId): Collection
    {
        return Candidate::where('user_id', $userId)->get();
    }

    public function findByStatus(string $status): Collection
    {
        return Candidate::where('status', $status)->get();
    }

    public function create(array $data): Candidate
    {
        return Candidate::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Candidate::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Candidate::destroy($id);
    }

    public function getApprovedCandidates(int $positionId): Collection
    {
        return Candidate::where('position_id', $positionId)
            ->where('status', 'approved')
            ->with('user')
            ->get();
    }

    public function getCandidateWithVotes(int $id): ?Candidate
    {
        return Candidate::with(['votes', 'user', 'position'])
            ->find($id);
    }

    public function findByOrganization(int $organizationId): Collection
    {
        return Candidate::where('organization_id', $organizationId)
            ->with(['user', 'position', 'position.election'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByOrganizationWithFilters(int $organizationId, array $filters = []): Collection
    {
        $query = Candidate::where('organization_id', $organizationId)
            ->with(['user', 'position', 'position.election']);

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by position
        if (!empty($filters['position_id'])) {
            $query->where('position_id', $filters['position_id']);
        }

        // Filter by election
        if (!empty($filters['election_id'])) {
            $query->whereHas('position', function ($q) use ($filters) {
                $q->where('election_id', $filters['election_id']);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function findByOrganizationWithFiltersPaginated(int $organizationId, array $filters = [], int $perPage = 15)
    {
        $query = Candidate::where('organization_id', $organizationId)
            ->with(['user', 'position', 'position.election']);

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by position
        if (!empty($filters['position_id'])) {
            $query->where('position_id', $filters['position_id']);
        }

        // Filter by election
        if (!empty($filters['election_id'])) {
            $query->whereHas('position', function ($q) use ($filters) {
                $q->where('election_id', $filters['election_id']);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findByUserAndPosition(int $userId, int $positionId): ?Candidate
    {
        return Candidate::where('user_id', $userId)
            ->where('position_id', $positionId)
            ->first();
    }
}
