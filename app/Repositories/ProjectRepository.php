<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent-backed read access for projects.
 */
class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * All projects sorted by name ascending.
     *
     * @return Collection<int, Project>
     */
    public function allOrderedByName(): Collection
    {
        return Project::query()->orderBy('name')->get();
    }

    /**
     * Find a project by id, or null if missing.
     */
    public function find(int $id): ?Project
    {
        return Project::query()->find($id);
    }

    /**
     * Find a project by id or throw a not-found exception.
     */
    public function findOrFail(int $id): Project
    {
        return Project::query()->findOrFail($id);
    }
}
