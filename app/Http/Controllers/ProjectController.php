<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Managers\Contracts\ProjectManagerInterface;
use Illuminate\Http\RedirectResponse;

/**
 * HTTP actions for creating projects.
 */
class ProjectController extends Controller
{
    /**
     * @param  ProjectManagerInterface  $projectManager  Persists new projects.
     */
    public function __construct(
        private ProjectManagerInterface $projectManager,
    ) {}

    /**
     * Store a newly created project and redirect to its task list.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $this->projectManager->create($request->validated());

        return redirect()
            ->route('tasks.index', ['project_id' => $project->id])
            ->with('status', 'Project created.');
    }
}
