<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDebtRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:credit_card,loan,mortgage,other'],
            'principal_amount' => ['nullable', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'current_balance' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'minimum_payment' => ['nullable', 'numeric', 'min:0.01', 'max:9999999.99'],
            'term_months' => ['nullable', 'integer', 'min:1', 'max:600'],
            'start_date' => ['required', 'date'],
            'payoff_target_date' => ['nullable', 'date', 'after:start_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The debt name is required.',
            'name.max' => 'The debt name must not exceed 255 characters.',
            'type.required' => 'The debt type is required.',
            'type.in' => 'The debt type must be credit card, loan, mortgage, or other.',
            'principal_amount.required' => 'The principal amount is required.',
            'principal_amount.min' => 'The principal amount must be at least $0.01.',
            'principal_amount.max' => 'The principal amount is too large.',
            'current_balance.required' => 'The current balance is required.',
            'current_balance.min' => 'The current balance must be at least $0.',
            'current_balance.max' => 'The current balance is too large.',
            'interest_rate.required' => 'The interest rate is required.',
            'interest_rate.min' => 'The interest rate must be at least 0%.',
            'interest_rate.max' => 'The interest rate must not exceed 100%.',
            'minimum_payment.min' => 'The minimum payment must be at least $0.01.',
            'term_months.min' => 'The term must be at least 1 month.',
            'term_months.max' => 'The term must not exceed 600 months.',
            'start_date.required' => 'The start date is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'payoff_target_date.date' => 'The payoff target date must be a valid date.',
            'payoff_target_date.after' => 'The payoff target date must be after the start date.',
            'notes.max' => 'Notes must not exceed 1000 characters.',
        ];
    }
}
