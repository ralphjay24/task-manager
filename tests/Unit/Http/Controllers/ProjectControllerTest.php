<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ProjectController;
use App\Http\Requests\StoreProjectRequest;
use App\Managers\Contracts\ProjectManagerInterface;
use App\Models\Project;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_store_delegates_to_manager_and_redirects(): void
    {
        $project = new Project;
        $project->forceFill(['id' => 8, 'name' => 'Alpha']);
        $project->exists = true;

        $manager = \Mockery::mock(ProjectManagerInterface::class);
        $manager->shouldReceive('create')->once()->with(['name' => 'Alpha'])->andReturn($project);

        $request = \Mockery::mock(StoreProjectRequest::class);
        $request->shouldReceive('validated')->once()->andReturn(['name' => 'Alpha']);

        $controller = new ProjectController($manager);
        $response   = $controller->store($request);

        $this->assertSame(route('tasks.index', ['project_id' => 8]), $response->getTargetUrl());
    }
}
