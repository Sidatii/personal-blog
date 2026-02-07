<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectRepository implements ProjectRepositoryInterface
{
    protected Project $project;

    /**
     * Constructor - inject Project model for dependency injection.
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get all projects ordered.
     */
    public function all(): Collection
    {
        return $this->project->ordered()->get();
    }

    /**
     * Find a project by slug.
     */
    public function findBySlug(string $slug): ?Project
    {
        return $this->project->where('slug', $slug)->first();
    }

    /**
     * Get projects by status.
     */
    public function getByStatus(string $status): Collection
    {
        return $this->project->byStatus($status)->ordered()->get();
    }

    /**
     * Get featured projects.
     */
    public function getFeatured(): Collection
    {
        return $this->project->featured()->ordered()->get();
    }
}
