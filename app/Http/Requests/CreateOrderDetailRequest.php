<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderDetailRequest extends FormRequest
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
            'quantity'              => 'required|integer|min:1',
            'unit_price'            => 'required|integer|min:1',
            'subtotal'              => 'required|integer|min:1',
            'product_snapshot_json' => 'required|longText|min:1',
        ];   
    }
}
