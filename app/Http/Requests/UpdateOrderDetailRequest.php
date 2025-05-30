<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderDetailRequest extends FormRequest
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
            'quantity'              => 'sometimes|integer|min:1',
            'unit_price'            => 'sometimes|integer|min:1',
            'subtotal'              => 'sometimes|integer|min:1',
            'product_snapshot_json' => 'sometimes|longText|min:1',
        ];   
    }
}
