<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'enterprise_id' => 'required|exists:enterprises,id',
            'brand_id' => 'required|exists:brands,id',
            'gender_id' => 'required|exists:genders,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'value' => 'required|integer',
        ];
    }
}
