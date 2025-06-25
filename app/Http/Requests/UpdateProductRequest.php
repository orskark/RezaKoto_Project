<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'enterprise_id' => 'sometimes|nullable|exists:enterprises,id',
            'brand_id' => 'sometimes|nullable|exists:brands,id',
            'gender_id' => 'sometimes|nullable|exists:genders,id',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'name' => 'sometimes|nullable|string|max:50',
            'description' => 'sometimes|nullable|string|max:255',
            'value' => 'sometimes|nullable|integer',
        ];
    }
}
