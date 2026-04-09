<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Managers\Contracts\TaskManagerInterface;
use App\Models\Task;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * HTTP actions for listing, creating, editing, deleting, and reordering tasks.
 */
class TaskController extends Controller
{
    /**
     * @param  ProjectRepositoryInterface  $projects  Read access for projects.
     * @param  TaskRepositoryInterface  $tasks  Read access for tasks.
     * @param  TaskManagerInterface  $taskManager  Write operations for tasks.
     */
    public function __construct(
        private ProjectRepositoryInterface $projects,
        private TaskRepositoryInterface $tasks,
        private TaskManagerInterface $taskManager,
    ) {}

    /**
     * Show the task list for the selected project (or the first project).
     */
    public function index(Request $request): View
    {
        $projectList = $this->projects->allOrderedByName();

        $projectId = $request->integer('project_id') ?: $projectList->first()?->id;

        $selectedProject = $projectId ? $this->projects->find($projectId) : null;

        $taskList = $selectedProject
            ? $this->tasks->orderedForProject($selectedProject->id)
            : collect();

        return view('tasks.index', [
            'projects'        => $projectList,
            'selectedProject' => $selectedProject,
            'tasks'           => $taskList,
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request): View
    {
        $projectList      = $this->projects->allOrderedByName();
        $defaultProjectId = $request->integer('project_id') ?: $projectList->first()?->id;

        return view('tasks.create', [
            'projects'         => $projectList,
            'defaultProjectId' => $defaultProjectId,
        ]);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->taskManager->create($data);

        return redirect()
            ->route('tasks.index', ['project_id' => $data['project_id']])
            ->with('status', 'Task created.');
    }

    /**
     * Show the form for editing the given task.
     */
    public function edit(Task $task): View
    {
        return view('tasks.edit', [
            'task'     => $task,
            'projects' => $this->projects->allOrderedByName(),
        ]);
    }

    /**
     * Update the given task in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $data = $request->validated();
        $this->taskManager->update($task, $data);

        return redirect()
            ->route('tasks.index', ['project_id' => $data['project_id']])
            ->with('status', 'Task updated.');
    }

    /**
     * Remove the given task from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $projectId = $task->project_id;
        $this->taskManager->delete($task);

        return redirect()
            ->route('tasks.index', ['project_id' => $projectId])
            ->with('status', 'Task deleted.');
    }

    /**
     * Persist task order for a project (priorities 1…n top to bottom).
     *
     * @return JsonResponse|RedirectResponse JSON when the client expects JSON; otherwise redirect back.
     */
    public function reorder(ReorderTasksRequest $request): JsonResponse|RedirectResponse
    {
        $validated  = $request->validated();
        $projectId  = (int) $validated['project_id'];
        $orderedIds = array_map('intval', $validated['task_ids']);

        $this->taskManager->reorder($projectId, $orderedIds);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()
            ->route('tasks.index', ['project_id' => $projectId])
            ->with('status', 'Order saved.');
    }
}
