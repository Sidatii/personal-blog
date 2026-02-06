<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var Category
     */
    protected Category $category;

    /**
     * Constructor - inject Category model for dependency injection.
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Find a category by slug.
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->category->where('slug', $slug)->first();
    }

    /**
     * Get all categories.
     */
    public function all(): Collection
    {
        return $this->category->all();
    }

    /**
     * Find or create a category by name and slug.
     */
    public function findOrCreate(string $name, string $slug): Category
    {
        return $this->category->firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );
    }

    /**
     * Get categories with post count populated.
     */
    public function withPostCount(): Collection
    {
        return $this->category
            ->withCount('posts')
            ->get();
    }
}
