@extends('layouts.app')

@section('title', 'Edit task')

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-medium text-slate-800">Edit task</h2>
        <p class="mt-1 text-sm text-slate-500">Changing project moves the task to the end of that project’s list.</p>

        <form action="{{ route('tasks.update', $task) }}" method="post" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="project_id" class="block text-sm font-medium text-slate-700">Project</label>
                <select name="project_id" id="project_id" required
                    class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected((int) old('project_id', $task->project_id) === $project->id)>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Task name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $task->name) }}" required
                    class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                    Save changes
                </button>
                <a href="{{ route('tasks.index', ['project_id' => $task->project_id]) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
