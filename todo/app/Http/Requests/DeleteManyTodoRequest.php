<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteManyTodoRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // validate the todos field of the request
        // make sure that the todos field is required
        // make sure that the todos field is an array
        // make sure that the todos field is an array of integers
        return [
            'todos' => 'required|array',
            'todos.*' => 'required|int'
        ];
    }
}
