<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'document_type_id' => 'required|exists:document_types,id',
            'complete_name' => 'required|string|min:10|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|max:50|confirmed',
            'identification' => 'required|string|min:6|max:20|unique:users,identification',
            'phone_number' => 'required|string|min:10|max:10|unique:users,phone_number',
            'address' => 'required|string|min:5|max:150',
        ];
    }
}
