<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
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
        // make sure that the content field is required when the status field is not present
        // make sure that the content field is a string
        // make sure that the content field is not more than 255 characters
        // validate the status field of the todo
        // make sure that the status field is required when the content field is not present
        // make sure that the status field is a string
        // make sure that the status field is completed. this can be extended for multiple status type
        return [
            'content' => 'required_without:status|string|max:255',
            'status' => 'required_without:content|string|in:completed'
        ];
    }
}
