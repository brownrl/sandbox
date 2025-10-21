<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StarWarsCharacter>
 */
class StarWarsCharacterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            'Luke Skywalker',
            'Han Solo',
            'Leia Organa',
            'Darth Vader',
            'Yoda',
            'Obi-Wan Kenobi',
            'Rey',
            'Kylo Ren',
            'Chewbacca',
            'R2-D2',
            'C-3PO',
            'Mace Windu',
            'Qui-Gon Jinn',
            'Padmé Amidala',
            'Anakin Skywalker',
            'Emperor Palpatine',
            'Boba Fett',
            'Jabba the Hutt',
            'Lando Calrissian',
            'Finn',
        ];

        $name = fake()->unique()->randomElement($names);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => fake()->sentence(),
        ];
    }
}
