<?php

namespace App\Managers;

use App\Managers\Contracts\ProjectManagerInterface;
use App\Models\Project;

/**
 * Persists {@see Project} records.
 */
class ProjectManager implements ProjectManagerInterface
{
    /**
     * Create and store a new project.
     *
     * @param  array{name: string}  $attributes
     */
    public function create(array $attributes): Project
    {
        return Project::query()->create($attributes);
    }
}
