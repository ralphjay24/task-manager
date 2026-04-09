<?php

namespace Tests\Unit\Repositories;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProjectRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProjectRepository;
    }

    public function test_all_ordered_by_name_sorts_alphabetically(): void
    {
        Project::query()->create(['name' => 'Zebra']);
        Project::query()->create(['name' => 'Alpha']);

        $names = $this->repository->allOrderedByName()->pluck('name')->all();

        $this->assertSame(['Alpha', 'Zebra'], $names);
    }

    public function test_find_returns_null_when_missing(): void
    {
        $this->assertNull($this->repository->find(999));
    }

    public function test_find_or_fail_throws_when_missing(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->findOrFail(999);
    }
}
