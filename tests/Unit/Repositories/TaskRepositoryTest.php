<?php

namespace Tests\Unit\Repositories;

use App\Models\Project;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TaskRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TaskRepository;
    }

    public function test_ordered_for_project_returns_tasks_sorted_by_priority_then_id(): void
    {
        $project = Project::query()->create(['name' => 'P']);

        Task::query()->create(['project_id' => $project->id, 'name' => 'Second', 'priority' => 2]);
        Task::query()->create(['project_id' => $project->id, 'name' => 'First', 'priority' => 1]);

        $ordered = $this->repository->orderedForProject($project->id);

        $this->assertSame(['First', 'Second'], $ordered->pluck('name')->all());
    }

    public function test_next_priority_for_project_is_one_when_empty(): void
    {
        $project = Project::query()->create(['name' => 'P']);

        $this->assertSame(1, $this->repository->nextPriorityForProject($project->id));
    }

    public function test_next_priority_for_project_is_max_plus_one(): void
    {
        $project = Project::query()->create(['name' => 'P']);
        Task::query()->create(['project_id' => $project->id, 'name' => 'A', 'priority' => 3]);

        $this->assertSame(4, $this->repository->nextPriorityForProject($project->id));
    }

    public function test_find_or_fail_returns_task(): void
    {
        $project = Project::query()->create(['name' => 'P']);
        $task    = Task::query()->create(['project_id' => $project->id, 'name' => 'A', 'priority' => 1]);

        $found = $this->repository->findOrFail($task->id);

        $this->assertTrue($found->is($task));
    }

    public function test_ordered_task_ids_for_project_matches_priority_order(): void
    {
        $project = Project::query()->create(['name' => 'P']);
        $t1      = Task::query()->create(['project_id' => $project->id, 'name' => 'B', 'priority' => 2]);
        $t2      = Task::query()->create(['project_id' => $project->id, 'name' => 'A', 'priority' => 1]);

        $ids = $this->repository->orderedTaskIdsForProject($project->id);

        $this->assertSame([$t2->id, $t1->id], $ids);
    }
}
