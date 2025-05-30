<?php

namespace App\Repositories\Interfaces;

use App\Models\VoterAccreditation;
use Illuminate\Database\Eloquent\Collection;

interface VoterAccreditationRepositoryInterface
{
    public function findById(int $id): ?VoterAccreditation;

    public function findByElection(int $electionId): Collection;

    public function findByUser(int $userId): Collection;

    public function findByStatus(string $status): Collection;

    public function create(array $data): VoterAccreditation;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function isUserAccredited(int $userId, int $electionId): bool;

    public function getPendingAccreditations(int $electionId): Collection;
}
