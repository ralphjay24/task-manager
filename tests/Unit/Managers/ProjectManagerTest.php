<?php

namespace Tests\Unit\Managers;

use App\Managers\ProjectManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_persists_project(): void
    {
        $manager = new ProjectManager;

        $project = $manager->create(['name' => 'Roadmap']);

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Roadmap']);
    }
}
