<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
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
        // validate the content field of the todo
        // make sure that the content field is required
        // make sure that the content field is a string
        // make sure that the content field is not more than 255 characters
        return [
            'content' => 'required|string|max:255',
        ];
    }
}
