<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent-backed read access for tasks.
 */
class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Find a task by primary key or abort with 404.
     */
    public function findOrFail(int $id): Task
    {
        return Task::query()->findOrFail($id);
    }

    /**
     * Tasks for a project ordered by priority, then id.
     *
     * @return Collection<int, Task>
     */
    public function orderedForProject(int $projectId): Collection
    {
        return Task::query()
            ->where('project_id', $projectId)
            ->orderBy('priority')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Next priority value for a new task at the end of a project’s list.
     */
    public function nextPriorityForProject(int $projectId): int
    {
        $max = Task::query()->where('project_id', $projectId)->max('priority');

        return (int) $max + 1;
    }

    /**
     * Task IDs in current priority order for a project.
     *
     * @return list<int>
     */
    public function orderedTaskIdsForProject(int $projectId): array
    {
        return Task::query()
            ->where('project_id', $projectId)
            ->orderBy('priority')
            ->orderBy('id', 'desc')
            ->pluck('id')
            ->all();
    }
}
