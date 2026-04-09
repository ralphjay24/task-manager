<?php

namespace Tests\Unit\Managers;

use App\Managers\TaskManager;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagerTest extends TestCase
{
    use RefreshDatabase;

    private TaskManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new TaskManager(
            new TaskRepository,
            new ProjectRepository,
        );
    }

    public function test_create_appends_priority_at_end_of_project(): void
    {
        $project = Project::query()->create(['name' => 'P']);
        Task::query()->create(['project_id' => $project->id, 'name' => 'Existing', 'priority' => 1]);

        $task = $this->manager->create([
            'project_id' => (string) $project->id,
            'name'       => 'New',
        ]);

        $this->assertSame(2, $task->priority);
        $this->assertSame('New', $task->name);
    }

    public function test_delete_renormalizes_remaining_priorities(): void
    {
        $project = Project::query()->create(['name' => 'P']);
        $a       = Task::query()->create(['project_id' => $project->id, 'name' => 'A', 'priority' => 1]);
        $b       = Task::query()->create(['project_id' => $project->id, 'name' => 'B', 'priority' => 2]);
        $c       = Task::query()->create(['project_id' => $project->id, 'name' => 'C', 'priority' => 3]);

        $this->manager->delete($b);

        $this->assertDatabaseMissing('tasks', ['id' => $b->id]);
        $this->assertSame(1, $a->fresh()->priority);
        $this->assertSame(2, $c->fresh()->priority);
    }

    public function test_reorder_sets_priorities_by_given_order(): void
    {
        $project = Project::query()->create(['name' => 'P']);
        $a       = Task::query()->create(['project_id' => $project->id, 'name' => 'A', 'priority' => 1]);
        $b       = Task::query()->create(['project_id' => $project->id, 'name' => 'B', 'priority' => 2]);

        $this->manager->reorder($project->id, [$b->id, $a->id]);

        $this->assertSame(1, $b->fresh()->priority);
        $this->assertSame(2, $a->fresh()->priority);
    }

    public function test_update_when_changing_project_moves_task_and_fixes_old_priorities(): void
    {
        $p1 = Project::query()->create(['name' => 'One']);
        $p2 = Project::query()->create(['name' => 'Two']);
        $t1 = Task::query()->create(['project_id' => $p1->id, 'name' => 'Stay', 'priority' => 1]);
        $t2 = Task::query()->create(['project_id' => $p1->id, 'name' => 'Move', 'priority' => 2]);

        $this->manager->update($t2, [
            'project_id' => (string) $p2->id,
            'name'       => 'Move',
        ]);

        $this->assertSame($p2->id, $t2->fresh()->project_id);
        $this->assertSame(1, $t2->fresh()->priority);
        $this->assertSame(1, $t1->fresh()->priority);
    }
}
