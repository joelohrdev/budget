<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'card_id' => ['required', 'exists:cards,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'type' => ['required', 'in:debit,credit'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'card_id.required' => 'The card is required.',
            'card_id.exists' => 'The selected card is invalid.',
            'category_id.exists' => 'The selected category is invalid.',
            'description.required' => 'The description is required.',
            'description.max' => 'The description must not exceed 255 characters.',
            'amount.required' => 'The amount is required.',
            'amount.min' => 'The amount must be at least 0.01.',
            'amount.numeric' => 'The amount must be a valid number.',
            'type.required' => 'The transaction type is required.',
            'type.in' => 'The transaction type must be either debit or credit.',
            'transaction_date.required' => 'The transaction date is required.',
            'transaction_date.date' => 'The transaction date must be a valid date.',
        ];
    }
}
