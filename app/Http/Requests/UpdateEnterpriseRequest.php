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
            'name' => 'sometimes|string|max:50',
            'description' => 'sometimes|string',
            'NIT' => 'sometimes|digits:9|unique:enterprises,NIT,' . $this->route('enterprise')->id,
            'phone_number' => 'sometimes|string|min:6|unique:enterprises,phone_number,' . $this->route('enterprise')->id,
            'address' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:enterprises,email,' . $this->route('enterprise')->id,
        ];
    }
}
