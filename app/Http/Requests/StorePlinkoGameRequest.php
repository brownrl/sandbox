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
            'final_slot' => ['required', 'integer', 'min:0', 'max:8'],
            'drop_x' => ['required', 'numeric'],
            'final_x' => ['required', 'numeric'],
            'horizontal_distance' => ['required', 'numeric'],
            'path' => ['required', 'array'],
            'fall_time_ms' => ['required', 'integer'],
            'peg_collisions' => ['required', 'integer'],
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
            'final_slot.required' => 'The final slot is required.',
            'final_slot.max' => 'Invalid final slot.',
        ];
    }
}
