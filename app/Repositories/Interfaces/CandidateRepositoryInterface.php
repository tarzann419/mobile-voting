<?php

namespace App\Repositories\Interfaces;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Collection;

interface CandidateRepositoryInterface
{
    public function findById(int $id): ?Candidate;

    public function findByPosition(int $positionId): Collection;

    public function findByUser(int $userId): Collection;

    public function findByStatus(string $status): Collection;

    public function create(array $data): Candidate;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function getApprovedCandidates(int $positionId): Collection;

    public function getCandidateWithVotes(int $id): ?Candidate;

    public function findByOrganization(int $organizationId): Collection;

    public function findByOrganizationWithFilters(int $organizationId, array $filters = []): Collection;

    public function findByOrganizationWithFiltersPaginated(int $organizationId, array $filters = [], int $perPage = 15);

    public function findByUserAndPosition(int $userId, int $positionId): ?Candidate;
}
