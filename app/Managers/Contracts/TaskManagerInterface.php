<?php

namespace App\Managers\Contracts;

use App\Models\Task;

/**
 * Contract for task write operations.
 */
interface TaskManagerInterface
{
    /**
     * Create a task with the next available priority in its project.
     *
     * @param  array{project_id: int|string, name: string}  $data
     */
    public function create(array $data): Task;

    /**
     * Update task fields and handle cross-project moves.
     *
     * @param  array{project_id: int|string, name: string}  $data
     */
    public function update(Task $task, array $data): void;

    /**
     * Delete a task and normalize sibling priorities.
     */
    public function delete(Task $task): void;

    /**
     * Apply explicit priority order for tasks in a project.
     *
     * @param  list<int>  $orderedTaskIds
     */
    public function reorder(int $projectId, array $orderedTaskIds): void;
}
