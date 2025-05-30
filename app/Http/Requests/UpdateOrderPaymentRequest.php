<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderPaymentRequest extends FormRequest
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
            'value' => 'sometimes|nullable|float|min:1',
            'transaction_reference' => 'sometimes|nullable|string|unique:order_payments,transaction_reference',
            'notes' => 'sometimes|nullable|text|min:5',
        ];
    }
}
