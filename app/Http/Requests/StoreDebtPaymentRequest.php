<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDebtPaymentRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'principal_amount' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'interest_amount' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'The payment amount is required.',
            'amount.min' => 'The payment amount must be at least $0.01.',
            'amount.max' => 'The payment amount is too large.',
            'principal_amount.required' => 'The principal amount is required.',
            'principal_amount.min' => 'The principal amount must be at least $0.',
            'principal_amount.max' => 'The principal amount is too large.',
            'interest_amount.required' => 'The interest amount is required.',
            'interest_amount.min' => 'The interest amount must be at least $0.',
            'interest_amount.max' => 'The interest amount is too large.',
            'payment_date.required' => 'The payment date is required.',
            'payment_date.date' => 'The payment date must be a valid date.',
            'notes.max' => 'Notes must not exceed 1000 characters.',
        ];
    }
}
