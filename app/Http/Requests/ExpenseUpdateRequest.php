<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property \App\Models\Expense $expense
 */

class ExpenseUpdateRequest extends FormRequest
{

    /* Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \Illuminate\Contracts\Auth\Guard $user */
        $user = auth();
        return $this->expense->user_id === $user->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:50|min:3',
            'description' => 'nullable|string|max:200',
            'amount' => 'sometimes|required|numeric',
            'category_id' => 'sometimes|required|exists:categories,id'
        ];
    }
}
