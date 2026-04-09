<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

/**
 * Read-only access to tasks.
 */
interface TaskRepositoryInterface
{
    /**
     * Find a task by primary key or fail.
     */
    public function findOrFail(int $id): Task;

    /**
     * Tasks belonging to a project in display order.
     *
     * @return Collection<int, Task>
     */
    public function orderedForProject(int $projectId): Collection;

    /**
     * Priority value for appending a task to a project.
     */
    public function nextPriorityForProject(int $projectId): int;

    /**
     * Ordered task primary keys for a project.
     *
     * @return list<int>
     */
    public function orderedTaskIdsForProject(int $projectId): array;
}
