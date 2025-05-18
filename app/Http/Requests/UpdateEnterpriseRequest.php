<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnterpriseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'NIT' => 'sometimes|digits:9|unique:enterprises,NIT',
            'phone_number' => 'sometimes|string|min:6|unique:enterprises,phone_number',
            'address' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:enterprises,email',
        ];
    }
}
