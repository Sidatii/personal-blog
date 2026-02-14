<?php

namespace App\Repositories\Contracts;

use App\Models\Certification;
use Illuminate\Support\Collection;

interface CertificationRepositoryInterface
{
    /**
     * Get all certifications ordered.
     */
    public function all(): Collection;

    /**
     * Get featured certifications.
     */
    public function featured(): Collection;

    /**
     * Find a certification by ID.
     */
    public function find(int $id): Certification;

    /**
     * Create a new certification.
     */
    public function create(array $data): Certification;

    /**
     * Update an existing certification.
     */
    public function update(int $id, array $data): Certification;

    /**
     * Delete a certification.
     */
    public function delete(int $id): void;
}
