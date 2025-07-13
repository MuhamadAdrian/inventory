<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessLocationRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('business_locations')->ignore($this->route('business_location'))],
            'code' => ['required', 'string', 'max:50', Rule::unique('business_locations')->ignore($this->route('business_location'))],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'type' => ['required', 'string', 'in:warehouse,store,office'],
        ];
    }
}
