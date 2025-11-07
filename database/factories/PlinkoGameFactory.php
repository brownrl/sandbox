<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlinkoGame>
 */
class PlinkoGameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'score' => $score,
            'final_slot' => $finalSlot,
            'path' => null,
        ];
    }

    /**
     * Get the score for a given slot (matches game logic).
     */
    private function getScoreForSlot(int $slot): int
    {
        $slots = [100, 500, 1000, 0, 10000, 0, 1000, 500, 100];

        return $slots[$slot] ?? 0;
    }

    /**
     * Set a specific user for this game.
     */
    public function forUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }
}
