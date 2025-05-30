<?php

namespace App\Repositories\Interfaces;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;

interface OrganizationRepositoryInterface
{
    public function findById(int $id): ?Organization;

    public function findBySlug(string $slug): ?Organization;

    public function findActive(): Collection;

    public function create(array $data): Organization;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function getWithElections(int $id): ?Organization;
}
