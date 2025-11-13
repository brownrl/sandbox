<?php

namespace Tests\Feature;

use App\Models\JokeRating;
use App\Models\YoMommaJoke;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class YoMommaBattleTest extends TestCase
{
    use RefreshDatabase;

    public function test_battle_page_loads(): void
    {
        $response = $this->get('/yo-momma-battle');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('YoMommaBattle')
            ->has('llmModels', 5)
        );
    }

    public function test_can_get_random_joke(): void
    {
        YoMommaJoke::factory()->create(['joke' => 'Yo momma so cool!']);

        $response = $this->get('/yo-momma-battle/random-joke');

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'joke']);
    }

    public function test_can_rate_joke(): void
    {
        $joke = YoMommaJoke::factory()->create();

        $response = $this->withoutMiddleware()
            ->postJson('/yo-momma-battle/rate', [
                'joke_id' => $joke->id,
                'llm_model_slug' => 'gpt-4o',
                'rating' => 5,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('joke_ratings', [
            'yo_momma_joke_id' => $joke->id,
            'llm_model_slug' => 'gpt-4o',
            'rating' => 5,
        ]);
    }

    public function test_rating_validation(): void
    {
        $joke = YoMommaJoke::factory()->create();

        $response = $this->withoutMiddleware()
            ->postJson('/yo-momma-battle/rate', [
                'joke_id' => $joke->id,
                'llm_model_slug' => 'gpt-4o',
                'rating' => 10,
            ]);

        $response->assertStatus(422);
    }
}
