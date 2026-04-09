<?php

namespace App\Managers;

use App\Managers\Contracts\TaskManagerInterface;
use App\Models\Task;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Coordinates task persistence: create, update, delete, and priority ordering per project.
 */
class TaskManager implements TaskManagerInterface
{
    /**
     * @param  TaskRepositoryInterface  $taskRepository  Task reads used for priorities and normalization.
     * @param  ProjectRepositoryInterface  $projectRepository  Loads projects for reorder transactions.
     */
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private ProjectRepositoryInterface $projectRepository,
    ) {}

    /**
     * Create a task at the end of the project’s priority list.
     *
     * @param  array{project_id: int|string, name: string}  $data
     */
    public function create(array $data): Task
    {
        $projectId          = (int) $data['project_id'];
        $data['project_id'] = $projectId;
        $data['priority']   = $this->taskRepository->nextPriorityForProject($projectId);

        return Task::query()->create($data);
    }

    /**
     * Update a task; moving it to another project appends it there and re-numbers the old project.
     *
     * @param  array{project_id: int|string, name: string}  $data
     */
    public function update(Task $task, array $data): void
    {
        $newProjectId      = (int) $data['project_id'];
        $previousProjectId = $task->project_id;

        if ($previousProjectId !== $newProjectId) {
            $data['project_id'] = $newProjectId;
            $data['priority']   = $this->taskRepository->nextPriorityForProject($newProjectId);
        }

        $task->update($data);

        if ($previousProjectId !== $newProjectId) {
            $this->normalizePrioritiesForProject($previousProjectId);
        }
    }

    /**
     * Delete a task and re-number remaining tasks in the same project.
     */
    public function delete(Task $task): void
    {
        $projectId = $task->project_id;
        $task->delete();
        $this->normalizePrioritiesForProject($projectId);
    }

    /**
     * Set task priorities to match the given ID order (top = 1).
     *
     * @param  list<int>  $orderedTaskIds
     */
    public function reorder(int $projectId, array $orderedTaskIds): void
    {
        if ($orderedTaskIds === []) {
            return;
        }

        $project = $this->projectRepository->findOrFail($projectId);

        DB::transaction(function () use ($project, $orderedTaskIds): void {
            $priority = 1;
            foreach ($orderedTaskIds as $taskId) {
                $project->tasks()->whereKey($taskId)->update(['priority' => $priority]);
                $priority++;
            }
        });
    }

    /**
     * Reassign contiguous priorities 1…n for all tasks in a project.
     */
    private function normalizePrioritiesForProject(int $projectId): void
    {
        $ids = $this->taskRepository->orderedTaskIdsForProject($projectId);

        if ($ids === []) {
            return;
        }

        $this->reorder($projectId, $ids);
    }
}
