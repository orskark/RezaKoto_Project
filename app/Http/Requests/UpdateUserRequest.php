<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'first_name' => 'sometimes|nullable|string|min:3|max:100',
            'middle_name' => 'sometimes|nullable|string|min:3|max:100',
            'last_name' => 'sometimes|nullable|string|min:3|max:100',
            'second_last_name' => 'sometimes|nullable|string|min:3|max:100',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $this->route('user')->id,
            'identification' => 'sometimes|string|max:20|unique:users,identification,' . $this->route('user')->id,
            'phone_number' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'document_type_id' => 'sometimes|exists:document_types,id',
        ];
    }
}
