<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBillRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'due_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The bill name is required.',
            'name.max' => 'The bill name must not exceed 255 characters.',
            'amount.required' => 'The amount is required.',
            'amount.min' => 'The amount must be at least $0.01.',
            'amount.max' => 'The amount must not exceed $999,999.99.',
            'due_date.required' => 'The due date is required.',
            'due_date.date' => 'The due date must be a valid date.',
        ];
    }
}
