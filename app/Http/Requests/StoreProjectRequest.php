<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for project creation validation.
 *
 * @package App\Http\Requests
 */
class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization is handled by ProjectPolicy
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'repository_url' => ['nullable', 'url', 'max:500'],
            'demo_url' => ['nullable', 'url', 'max:500'],
            'notes' => ['nullable', 'string'],
            'team_members' => ['nullable', 'array'],
            'team_members.*' => ['integer', 'exists:users,id'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'Please select a company.',
            'company_id.exists' => 'The selected company does not exist.',
            'name.required' => 'Please enter a project name.',
            'name.max' => 'Project name must not exceed 255 characters.',
            'status.required' => 'Please select a project status.',
            'status.in' => 'Invalid project status selected.',
            'priority.required' => 'Please select a project priority.',
            'priority.in' => 'Invalid project priority selected.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'End date must be equal to or after the start date.',
            'budget.numeric' => 'Budget must be a valid number.',
            'budget.min' => 'Budget must be at least 0.',
            'budget.max' => 'Budget must not exceed 999,999,999.99.',
            'repository_url.url' => 'Please enter a valid repository URL.',
            'repository_url.max' => 'Repository URL must not exceed 500 characters.',
            'demo_url.url' => 'Please enter a valid demo URL.',
            'demo_url.max' => 'Demo URL must not exceed 500 characters.',
            'team_members.array' => 'Team members must be an array.',
            'team_members.*.exists' => 'One or more selected team members do not exist.',
        ];
    }
}
