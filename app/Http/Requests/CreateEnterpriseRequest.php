<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEnterpriseRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'NIT' => 'required|digits:9|unique:enterprises,NIT',
            'phone_number' => 'required|string|min:6|unique:enterprises,phone_number',
            'address' => 'required|string|max:255',
            'email' => 'required|email|unique:enterprises,email',
        ];
    }
}
