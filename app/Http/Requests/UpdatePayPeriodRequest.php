<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayPeriodRequest extends FormRequest
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
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'debit_card_budget' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'credit_card_budget' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'The start date is required.',
            'start_date.before' => 'The start date must be before the end date.',
            'end_date.required' => 'The end date is required.',
            'end_date.after' => 'The end date must be after the start date.',
            'debit_card_budget.required' => 'The debit card budget is required.',
            'debit_card_budget.min' => 'The debit card budget must be at least 0.',
            'credit_card_budget.required' => 'The credit card budget is required.',
            'credit_card_budget.min' => 'The credit card budget must be at least 0.',
        ];
    }
}
