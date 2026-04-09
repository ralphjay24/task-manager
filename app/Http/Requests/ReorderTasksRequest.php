<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Validates drag-and-drop reorder payloads for a project’s tasks.
 */
class ReorderTasksRequest extends FormRequest
{
    /**
     * Determine if the user is allowed to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Base validation rules for project id and ordered task ids.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'task_ids'   => ['required', 'array'],
            'task_ids.*' => ['integer', 'distinct', 'exists:tasks,id'],
        ];
    }

    /**
     * Ensure submitted task ids match exactly the tasks in the given project.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $projectId = (int) $this->input('project_id');
            $submitted = array_map('intval', $this->input('task_ids', []));

            $expected = Task::query()
                ->where('project_id', $projectId)
                ->orderBy('id')
                ->pluck('id')
                ->map(fn (int|string $id): int => (int) $id)
                ->all();

            $a = $expected;
            $b = $submitted;
            sort($a);
            sort($b);

            if ($a !== $b) {
                $validator->errors()->add(
                    'task_ids',
                    'The task list must contain exactly the tasks belonging to this project.'
                );
            }
        });
    }
}
