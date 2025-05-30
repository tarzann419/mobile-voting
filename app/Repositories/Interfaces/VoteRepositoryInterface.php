<?php

namespace App\Repositories\Interfaces;

use App\Models\Vote;
use Illuminate\Database\Eloquent\Collection;

interface VoteRepositoryInterface
{
    public function findById(int $id): ?Vote;

    public function findByElection(int $electionId): Collection;

    public function findByUser(int $userId): Collection;

    public function create(array $data): Vote;

    public function hasUserVotedForPosition(int $userId, int $positionId): bool;

    public function getVoteCountByCandidate(int $candidateId): int;

    public function getVotesByPosition(int $positionId): Collection;

    public function getRealTimeResults(int $electionId): array;
}
