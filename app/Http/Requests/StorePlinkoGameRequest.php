<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlinkoGameRequest extends FormRequest
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
            'score' => ['required', 'integer', 'min:0', 'in:0,100,500,1000,10000'],
            'drop_position' => ['required', 'integer', 'min:0', 'max:8'],
            'final_slot' => ['required', 'integer', 'min:0', 'max:8'],
            'path' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'score.required' => 'The game score is required.',
            'score.in' => 'Invalid score value.',
            'drop_position.required' => 'The drop position is required.',
            'drop_position.max' => 'Invalid drop position.',
            'final_slot.required' => 'The final slot is required.',
            'final_slot.max' => 'Invalid final slot.',
        ];
    }
}
