<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Support\Collection;

interface ProjectRepositoryInterface
{
    /**
     * Get all projects ordered.
     */
    public function all(): Collection;

    /**
     * Find a project by slug.
     */
    public function findBySlug(string $slug): ?Project;

    /**
     * Get projects by status.
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get featured projects.
     */
    public function getFeatured(): Collection;
}
