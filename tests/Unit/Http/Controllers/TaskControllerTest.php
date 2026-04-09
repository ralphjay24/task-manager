<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\TaskController;
use App\Http\Requests\ReorderTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Managers\Contracts\TaskManagerInterface;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_index_builds_view_from_repositories(): void
    {
        $project = new Project;
        $project->forceFill(['id' => 5, 'name' => 'Work']);
        $project->exists = true;

        $projectList = new EloquentCollection([$project]);

        $projectRepo = \Mockery::mock(ProjectRepositoryInterface::class);
        $projectRepo->shouldReceive('allOrderedByName')->once()->andReturn($projectList);
        $projectRepo->shouldReceive('find')->once()->with(5)->andReturn($project);

        $task = new Task;
        $task->forceFill(['id' => 1, 'name' => 'T', 'project_id' => 5, 'priority' => 1]);
        $task->exists = true;
        $taskList     = new EloquentCollection([$task]);

        $taskRepo = \Mockery::mock(TaskRepositoryInterface::class);
        $taskRepo->shouldReceive('orderedForProject')->once()->with(5)->andReturn($taskList);

        $manager = \Mockery::mock(TaskManagerInterface::class);

        $controller = new TaskController($projectRepo, $taskRepo, $manager);
        $request    = Request::create('/tasks', 'GET', ['project_id' => 5]);

        $view = $controller->index($request);

        $this->assertSame('tasks.index', $view->name());
        $this->assertSame($projectList, $view->getData()['projects']);
        $this->assertSame($project, $view->getData()['selectedProject']);
        $this->assertSame($taskList, $view->getData()['tasks']);
    }

    public function test_store_delegates_to_manager_and_redirects(): void
    {
        $projectRepo = \Mockery::mock(ProjectRepositoryInterface::class);
        $taskRepo    = \Mockery::mock(TaskRepositoryInterface::class);
        $manager     = \Mockery::mock(TaskManagerInterface::class);
        $manager->shouldReceive('create')->once()->with([
            'project_id' => '3',
            'name'       => 'Buy milk',
        ]);

        $request = \Mockery::mock(StoreTaskRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'project_id' => '3',
            'name'       => 'Buy milk',
        ]);

        $controller = new TaskController($projectRepo, $taskRepo, $manager);
        $response   = $controller->store($request);

        $this->assertSame(route('tasks.index', ['project_id' => 3]), $response->getTargetUrl());
    }

    public function test_destroy_delegates_to_manager(): void
    {
        $projectRepo = \Mockery::mock(ProjectRepositoryInterface::class);
        $taskRepo    = \Mockery::mock(TaskRepositoryInterface::class);
        $manager     = \Mockery::mock(TaskManagerInterface::class);

        $task = new Task;
        $task->forceFill(['id' => 9, 'project_id' => 2, 'name' => 'X', 'priority' => 1]);
        $task->exists = true;

        $manager->shouldReceive('delete')->once()->with($task);

        $controller = new TaskController($projectRepo, $taskRepo, $manager);
        $response   = $controller->destroy($task);

        $this->assertSame(route('tasks.index', ['project_id' => 2]), $response->getTargetUrl());
    }

    public function test_update_delegates_to_manager(): void
    {
        $projectRepo = \Mockery::mock(ProjectRepositoryInterface::class);
        $taskRepo    = \Mockery::mock(TaskRepositoryInterface::class);
        $manager     = \Mockery::mock(TaskManagerInterface::class);

        $task = new Task;
        $task->forceFill(['id' => 4, 'project_id' => 1, 'name' => 'Old', 'priority' => 1]);
        $task->exists = true;

        $payload = ['project_id' => '1', 'name' => 'New'];

        $request = \Mockery::mock(UpdateTaskRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($payload);

        $manager->shouldReceive('update')->once()->with($task, $payload);

        $controller = new TaskController($projectRepo, $taskRepo, $manager);
        $response   = $controller->update($request, $task);

        $this->assertSame(route('tasks.index', ['project_id' => 1]), $response->getTargetUrl());
    }

    public function test_reorder_delegates_to_manager_and_returns_json_when_requested(): void
    {
        $projectRepo = \Mockery::mock(ProjectRepositoryInterface::class);
        $taskRepo    = \Mockery::mock(TaskRepositoryInterface::class);
        $manager     = \Mockery::mock(TaskManagerInterface::class);
        $manager->shouldReceive('reorder')->once()->with(7, [10, 11]);

        $request = \Mockery::mock(ReorderTasksRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'project_id' => 7,
            'task_ids'   => [10, 11],
        ]);
        $request->shouldReceive('wantsJson')->once()->andReturn(true);

        $controller = new TaskController($projectRepo, $taskRepo, $manager);
        $response   = $controller->reorder($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['ok' => true], $response->getData(true));
    }
}
