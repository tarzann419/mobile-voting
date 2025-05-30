<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    public function findById(int $id): ?Organization
    {
        return Organization::find($id);
    }

    public function findBySlug(string $slug): ?Organization
    {
        return Organization::where('slug', $slug)->first();
    }

    public function findActive(): Collection
    {
        return Organization::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('subscription_expires_at')
                    ->orWhere('subscription_expires_at', '>', now());
            })
            ->get();
    }

    public function create(array $data): Organization
    {
        return Organization::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Organization::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Organization::destroy($id);
    }

    public function getWithElections(int $id): ?Organization
    {
        return Organization::with(['elections', 'users'])->find($id);
    }
}
