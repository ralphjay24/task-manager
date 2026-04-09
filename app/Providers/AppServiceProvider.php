<?php

namespace App\Providers;

use App\Managers\Contracts\ProjectManagerInterface;
use App\Managers\Contracts\TaskManagerInterface;
use App\Managers\ProjectManager;
use App\Managers\TaskManager;
use App\Models\Task;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Registers application bindings and route model resolution.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register repository and manager interface bindings.
     */
    public function register(): void
    {
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(ProjectManagerInterface::class, ProjectManager::class);
        $this->app->bind(TaskManagerInterface::class, TaskManager::class);
    }

    /**
     * Resolve the `{task}` route parameter via the task repository.
     */
    public function boot(): void
    {
        Route::bind('task', function (string $value): Task {
            return $this->app->make(TaskRepositoryInterface::class)->findOrFail((int) $value);
        });
    }
}
