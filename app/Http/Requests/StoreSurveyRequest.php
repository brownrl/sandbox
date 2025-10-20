<?php

namespace App\Http\Requests;

use App\Models\StarWarsCharacter;
use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'character' => ['required', 'string', 'exists:star_wars_characters,slug'],
            'questions' => ['required', 'array', 'size:5'],
            'questions.*' => ['required', 'integer'],
            'responses' => ['required', 'array', 'size:5'],
            'responses.*' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    /**
     * Get custom error messages for the validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Please enter your first name.',
            'character.required' => 'Please select a Star Wars character.',
            'character.exists' => 'The selected Star Wars character is invalid.',
            'questions.required' => 'Survey questions are required.',
            'questions.size' => 'Exactly 5 questions must be answered.',
            'responses.required' => 'Please answer all survey questions.',
            'responses.size' => 'Please answer exactly 5 questions.',
            'responses.*.required' => 'Please provide a response for each question.',
            'responses.*.integer' => 'Please select a valid rating.',
            'responses.*.between' => 'Please select a rating between 1 and 10.',
        ];
    }
}
