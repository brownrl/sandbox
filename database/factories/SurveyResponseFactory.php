<?php

namespace Database\Factories;

use App\Models\StarWarsCharacter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SurveyResponse>
 */
class SurveyResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $characterSlugs = StarWarsCharacter::pluck('slug')->toArray();

        return [
            'first_name' => fake()->firstName(),
            'character' => fake()->randomElement($characterSlugs),
            'questions' => [1, 2, 3, 4, 5], // Will be overridden in tests
            'responses' => [
                fake()->numberBetween(1, 10),
                fake()->numberBetween(1, 10),
                fake()->numberBetween(1, 10),
                fake()->numberBetween(1, 10),
                fake()->numberBetween(1, 10),
            ],
        ];
    }
}
