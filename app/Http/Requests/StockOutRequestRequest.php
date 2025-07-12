<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockOutRequestRequest extends FormRequest
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
            'request_date' => ['required', 'date'],
            'desired_arrival_date' => ['nullable', 'date', 'after_or_equal:request_date'],
            'sender_id' => ['required', 'exists:business_locations,id'],
            'receiver_id' => ['required', 'exists:business_locations,id', 'different:sender_id'],
            'notes' => ['nullable', 'string', 'max:500'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
