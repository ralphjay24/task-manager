<?php

namespace App\Managers\Contracts;

use App\Models\Project;

/**
 * Contract for project write operations.
 */
interface ProjectManagerInterface
{
    /**
     * Persist a new project.
     *
     * @param  array{name: string}  $attributes
     */
    public function create(array $attributes): Project;
}
