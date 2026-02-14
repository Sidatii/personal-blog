<?php

namespace App\Repositories\Eloquent;

use App\Models\Certification;
use App\Repositories\Contracts\CertificationRepositoryInterface;
use Illuminate\Support\Collection;

class CertificationRepository implements CertificationRepositoryInterface
{
    /**
     * Get all certifications ordered.
     */
    public function all(): Collection
    {
        return Certification::ordered()->get();
    }

    /**
     * Get featured certifications ordered.
     */
    public function featured(): Collection
    {
        return Certification::featured()->ordered()->get();
    }

    /**
     * Find a certification by ID or throw.
     */
    public function find(int $id): Certification
    {
        return Certification::findOrFail($id);
    }

    /**
     * Create a new certification.
     */
    public function create(array $data): Certification
    {
        return Certification::create($data);
    }

    /**
     * Update an existing certification and return fresh instance.
     */
    public function update(int $id, array $data): Certification
    {
        $certification = Certification::findOrFail($id);
        $certification->update($data);

        return $certification->fresh();
    }

    /**
     * Delete a certification (soft delete).
     */
    public function delete(int $id): void
    {
        $certification = Certification::findOrFail($id);
        $certification->delete();
    }
}
