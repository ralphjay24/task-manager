<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates input when updating an existing task.
 */
class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is allowed to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for updating a task.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'name'       => ['required', 'string', 'max:255'],
        ];
    }
}
