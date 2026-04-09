<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

/**
 * Read-only access to projects.
 */
interface ProjectRepositoryInterface
{
    /**
     * All projects ordered alphabetically by name.
     *
     * @return Collection<int, Project>
     */
    public function allOrderedByName(): Collection;

    /**
     * Find a project by id, if it exists.
     */
    public function find(int $id): ?Project;

    /**
     * Find a project by id or abort.
     */
    public function findOrFail(int $id): Project;
}
