<?php

namespace Tests\Feature;

use App\Models\PlinkoGame;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PlinkoGameTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_plinko_index_returns_inertia_response(): void
    {
        $response = $this->get(route('plinko'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Plinko')
            ->has('recentGames')
            ->has('highScores')
            ->has('statistics')
        );
    }

    public function test_plinko_index_shows_recent_games(): void
    {
        PlinkoGame::factory()->count(15)->create();

        $response = $this->get(route('plinko'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Plinko')
            ->has('recentGames', 10)
            ->where('recentGames.0.score', fn ($score) => $score >= 0)
        );
    }

    public function test_plinko_index_shows_high_scores(): void
    {
        PlinkoGame::factory()->create(['score' => 10000]);
        PlinkoGame::factory()->create(['score' => 5000]);
        PlinkoGame::factory()->create(['score' => 100]);

        $response = $this->get(route('plinko'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Plinko')
            ->has('highScores', 3)
            ->where('highScores.0.score', 10000)
            ->where('highScores.1.score', 5000)
        );
    }

    public function test_plinko_index_calculates_statistics(): void
    {
        PlinkoGame::factory()->create(['score' => 10000]);
        PlinkoGame::factory()->create(['score' => 1000]);
        PlinkoGame::factory()->create(['score' => 500]);

        $response = $this->get(route('plinko'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Plinko')
            ->where('statistics.total_games', 3)
            ->where('statistics.total_winnings', 11500)
            ->where('statistics.highest_score', 10000)
        );
    }

    public function test_plinko_store_creates_game_with_valid_data(): void
    {
        $validData = [
            'score' => 10000,
            'final_slot' => 4,
            'drop_x' => 300.0,
            'final_x' => 310.0,
            'horizontal_distance' => 10.0,
            'path' => [
                ['x' => 300, 'y' => 100],
                ['x' => 310, 'y' => 150],
            ],
            'fall_time_ms' => 2000,
            'peg_collisions' => 15,
        ];

        $response = $this->post(route('plinko.store'), $validData);

        $response->assertRedirect();

        $this->assertDatabaseHas('plinko_games', [
            'score' => 10000,
            'final_slot' => 4,
        ]);
    }

    public function test_plinko_store_saves_game_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $validData = [
            'score' => 1000,
            'final_slot' => 2,
            'drop_x' => 200.0,
            'final_x' => 210.0,
            'horizontal_distance' => 10.0,
            'path' => [['x' => 200, 'y' => 100]],
            'fall_time_ms' => 1500,
            'peg_collisions' => 10,
        ];

        $response = $this->actingAs($user)->post(route('plinko.store'), $validData);

        $response->assertRedirect();

        $this->assertDatabaseHas('plinko_games', [
            'user_id' => $user->id,
            'score' => 1000,
        ]);
    }

    public function test_plinko_store_saves_game_for_guest(): void
    {
        $validData = [
            'score' => 500,
            'final_slot' => 1,
            'drop_x' => 150.0,
            'final_x' => 160.0,
            'horizontal_distance' => 10.0,
            'path' => [['x' => 150, 'y' => 100]],
            'fall_time_ms' => 1200,
            'peg_collisions' => 8,
        ];

        $response = $this->post(route('plinko.store'), $validData);

        $response->assertRedirect();

        $this->assertDatabaseHas('plinko_games', [
            'user_id' => null,
            'score' => 500,
        ]);
    }

    public function test_plinko_store_validates_required_fields(): void
    {
        $response = $this->post(route('plinko.store'), []);

        $response->assertSessionHasErrors(['score', 'final_slot', 'drop_x', 'final_x', 'horizontal_distance', 'path', 'fall_time_ms', 'peg_collisions']);
    }

    public function test_plinko_store_validates_score_values(): void
    {
        $invalidData = [
            'score' => 999,
            'final_slot' => 4,
            'drop_x' => 300.0,
            'final_x' => 310.0,
            'horizontal_distance' => 10.0,
            'path' => [['x' => 300, 'y' => 100]],
            'fall_time_ms' => 2000,
            'peg_collisions' => 15,
        ];

        $response = $this->post(route('plinko.store'), $invalidData);

        $response->assertSessionHasErrors(['score']);
    }

    public function test_plinko_store_accepts_valid_score_values(): void
    {
        $validScores = [0, 100, 500, 1000, 10000];

        foreach ($validScores as $score) {
            $validData = [
                'score' => $score,
                'final_slot' => 4,
                'drop_x' => 300.0,
                'final_x' => 310.0,
                'horizontal_distance' => 10.0,
                'path' => [['x' => 300, 'y' => 100]],
                'fall_time_ms' => 2000,
                'peg_collisions' => 15,
            ];

            $response = $this->post(route('plinko.store'), $validData);

            $response->assertRedirect();
        }
    }

    public function test_plinko_game_has_user_relationship(): void
    {
        $user = User::factory()->create();
        $game = PlinkoGame::factory()->forUser($user->id)->create();

        $this->assertInstanceOf(User::class, $game->user);
        $this->assertEquals($user->id, $game->user->id);
    }

    public function test_plinko_factory_generates_valid_scores(): void
    {
        $game = PlinkoGame::factory()->create();

        $validScores = [0, 100, 500, 1000, 10000];
        $this->assertContains($game->score, $validScores);
    }

    public function test_plinko_factory_generates_valid_positions(): void
    {
        $game = PlinkoGame::factory()->create();

        $this->assertGreaterThanOrEqual(0, $game->final_slot);
        $this->assertLessThanOrEqual(8, $game->final_slot);
    }

    public function test_plinko_path_is_cast_to_array(): void
    {
        $path = [
            ['x' => 100, 'y' => 200],
            ['x' => 110, 'y' => 250],
        ];

        $game = PlinkoGame::factory()->create([
            'path' => $path,
        ]);

        $this->assertIsArray($game->path);
        $this->assertEquals($path, $game->path);
    }

    public function test_plinko_statistics_handles_no_games(): void
    {
        $response = $this->get(route('plinko'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Plinko')
            ->where('statistics.total_games', 0)
            ->where('statistics.total_winnings', 0)
        );
    }

    public function test_plinko_index_limits_recent_games_to_ten(): void
    {
        PlinkoGame::factory()->count(20)->create();

        $response = $this->get(route('plinko'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Plinko')
            ->has('recentGames', 10)
        );
    }

    public function test_plinko_index_limits_high_scores_to_ten(): void
    {
        PlinkoGame::factory()->count(20)->create();

        $response = $this->get(route('plinko'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Plinko')
            ->has('highScores', 10)
        );
    }

    public function test_plinko_recent_games_are_newest_first(): void
    {
        $oldGame = PlinkoGame::factory()->create(['created_at' => now()->subDays(2)]);
        $newGame = PlinkoGame::factory()->create(['created_at' => now()]);

        $response = $this->get(route('plinko'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Plinko')
            ->where('recentGames.0.id', $newGame->id)
            ->where('recentGames.1.id', $oldGame->id)
        );
    }
}
