<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
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
            'title' => 'sometimes|string|min:3|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The tag title is required.',
            'title.min' => 'The tag title must be at least 3 characters long.',
            'title.max' => 'The tag title cannot be longer than 20 characters.',
        ];
    }
}
