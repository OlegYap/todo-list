<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:20',
            'text' => 'required|string|max:200',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The task title is required.',
            'title.min' => 'The task title must be at least 3 characters long.',
            'title.max' => 'The task title cannot be longer than 20 characters.',
            'text.required' => 'The task description is required.',
            'text.max' => 'The task description cannot be longer than 200 characters.',
            'tags.*.exists' => 'One or more selected tags do not exist.',
        ];
    }
}
