@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
    @if ($projects->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <p class="text-slate-600">Create a project to start adding tasks.</p>
            <form action="{{ route('projects.store') }}" method="post" class="mx-auto mt-6 flex max-w-md flex-col gap-3 sm:flex-row">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Project name"
                    class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                    Create project
                </button>
            </form>
        </div>
    @else
        <div class="mb-8 flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-end sm:justify-between">
            <form method="get" action="{{ route('tasks.index') }}" class="flex flex-col gap-2 sm:min-w-[240px]">
                <label for="project_id" class="text-xs font-medium uppercase tracking-wide text-slate-500">Project</label>
                <select name="project_id" id="project_id" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected($selectedProject && $selectedProject->id === $project->id)>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            <form action="{{ route('projects.store') }}" method="post" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                @csrf
                <div class="flex flex-col gap-1">
                    <label for="new_project_name" class="text-xs font-medium uppercase tracking-wide text-slate-500">New project</label>
                    <input type="text" name="name" id="new_project_name" value="{{ old('name') }}" required placeholder="Name"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <button type="submit" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                    Add
                </button>
            </form>
        </div>

        @if ($selectedProject)
            <div class="mb-6 flex items-center justify-between gap-4">
                <h2 class="text-lg font-medium text-slate-800">{{ $selectedProject->name }}</h2>
                <a href="{{ route('tasks.create', ['project_id' => $selectedProject->id]) }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                    New task
                </a>
            </div>

            @if ($tasks->isEmpty())
                <p class="rounded-lg border border-dashed border-slate-300 bg-white px-4 py-8 text-center text-sm text-slate-500">
                    No tasks yet. Create one to get started.
                </p>
            @else
                <ul id="task-list" class="space-y-2" data-project-id="{{ $selectedProject->id }}" data-reorder-url="{{ route('tasks.reorder') }}">
                    @foreach ($tasks as $task)
                        <li class="task-item flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3 shadow-sm"
                            data-task-id="{{ $task->id }}">
                            <span class="drag-handle cursor-grab select-none text-slate-400 active:cursor-grabbing" title="Drag to reorder" aria-hidden="true">⠿</span>
                            <span class="priority-badge flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">
                                {{ $loop->iteration }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-slate-800">{{ $task->name }}</p>
                                <p class="text-xs text-slate-400">Created {{ $task->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <a href="{{ route('tasks.edit', $task) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Edit</a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="post" class="inline"
                                    onsubmit="return confirm('Delete this task?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">Delete</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif
    @endif
@endsection

@push('scripts')
    @if ($selectedProject && $tasks->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const list = document.getElementById('task-list');
                if (!list || typeof Sortable === 'undefined') return;

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const projectId = list.dataset.projectId;
                const reorderUrl = list.dataset.reorderUrl;

                function refreshPriorityLabels() {
                    list.querySelectorAll('.priority-badge').forEach(function (el, i) {
                        el.textContent = String(i + 1);
                    });
                }

                new Sortable(list, {
                    animation: 150,
                    handle: '.drag-handle',
                    onEnd: function () {
                        refreshPriorityLabels();
                        const ids = Array.from(list.querySelectorAll('.task-item')).map(function (row) {
                            return parseInt(row.dataset.taskId, 10);
                        });

                        fetch(reorderUrl, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ project_id: parseInt(projectId, 10), task_ids: ids }),
                        }).catch(function () {
                            window.location.reload();
                        });
                    },
                });
            });
        </script>
    @endif
@endpush
