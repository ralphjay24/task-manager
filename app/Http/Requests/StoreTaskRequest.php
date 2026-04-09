<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates input when storing a new task.
 */
class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is allowed to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for creating a task.
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
