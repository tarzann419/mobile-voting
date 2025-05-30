<?php

namespace App\Repositories\Interfaces;

use App\Models\Election;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ElectionRepositoryInterface
{
    public function findById(int $id): ?Election;

    public function findByOrganization(int $organizationId): Collection;

    public function findActiveElections(): Collection;

    public function findByStatus(string $status): Collection;

    public function create(array $data): Election;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function paginate(int $organizationId, int $perPage = 15): LengthAwarePaginator;

    public function getWithPositionsAndCandidates(int $id): ?Election;

    public function getElectionResults(int $id): array;

    public function getElectionsWithResults(int $organizationId): Collection;

    public function getElectionsForReports(int $organizationId): Collection;
}
