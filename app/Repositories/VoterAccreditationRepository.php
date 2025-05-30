<?php

namespace App\Repositories;

use App\Models\VoterAccreditation;
use App\Repositories\Interfaces\VoterAccreditationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class VoterAccreditationRepository implements VoterAccreditationRepositoryInterface
{
    public function findById(int $id): ?VoterAccreditation
    {
        return VoterAccreditation::find($id);
    }

    public function findByElection(int $electionId): Collection
    {
        return VoterAccreditation::where('election_id', $electionId)->get();
    }

    public function findByUser(int $userId): Collection
    {
        return VoterAccreditation::where('user_id', $userId)->get();
    }

    public function findByStatus(string $status): Collection
    {
        return VoterAccreditation::where('status', $status)->get();
    }

    public function create(array $data): VoterAccreditation
    {
        return VoterAccreditation::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return VoterAccreditation::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return VoterAccreditation::destroy($id);
    }

    public function isUserAccredited(int $userId, int $electionId): bool
    {
        return VoterAccreditation::where('user_id', $userId)
            ->where('election_id', $electionId)
            ->where('status', 'approved')
            ->exists();
    }

    public function getPendingAccreditations(int $electionId): Collection
    {
        return VoterAccreditation::where('election_id', $electionId)
            ->where('status', 'pending')
            ->with('user')
            ->get();
    }
}
