<?php

namespace Tests\Feature;

use App\Models\StarWarsCharacter;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SurveyControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test Star Wars characters
        StarWarsCharacter::factory()->count(10)->create();

        // Create some test survey questions
        SurveyQuestion::factory()->count(10)->create([
            'is_active' => true,
        ]);
    }

    public function test_survey_show_returns_inertia_response(): void
    {
        $response = $this->get(route('survey'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Survey')
            ->has('questions', 5)
            ->has('questions.0.id')
            ->has('questions.0.question')
            ->has('characters')
        );
    }

    public function test_survey_show_stores_questions_in_session(): void
    {
        $this->assertFalse(session()->has('survey_questions'));

        $response = $this->get(route('survey'));

        $response->assertSessionHas('survey_questions');
        $sessionQuestions = session('survey_questions');
        $this->assertCount(5, $sessionQuestions);
    }

    public function test_survey_show_reuses_session_questions(): void
    {
        // First request stores questions in session
        $this->get(route('survey'));
        $firstSessionQuestions = session('survey_questions');

        // Second request should reuse the same questions
        $this->get(route('survey'));
        $secondSessionQuestions = session('survey_questions');

        $this->assertEquals($firstSessionQuestions->pluck('id')->toArray(),
            $secondSessionQuestions->pluck('id')->toArray());
    }

    public function test_survey_store_creates_response_with_valid_data(): void
    {
        // Set up session questions
        $questions = SurveyQuestion::limit(5)->get();
        session(['survey_questions' => $questions]);

        $character = StarWarsCharacter::first();

        $validData = [
            'first_name' => fake()->firstName(),
            'character' => $character->slug,
            'questions' => $questions->pluck('id')->toArray(),
            'responses' => [8, 7, 9, 6, 10],
        ];

        $response = $this->post(route('survey.store'), $validData);

        $response->assertRedirect(route('survey.success'));

        $this->assertDatabaseHas('survey_responses', [
            'first_name' => $validData['first_name'],
            'character' => $validData['character'],
        ]);

        // Verify session questions are cleared
        $this->assertFalse(session()->has('survey_questions'));
    }

    public function test_survey_store_rejects_mismatched_questions(): void
    {
        // Set up session questions
        $sessionQuestions = SurveyQuestion::limit(5)->get();
        session(['survey_questions' => $sessionQuestions]);

        // Use different question IDs
        $differentQuestions = SurveyQuestion::skip(5)->limit(5)->get();
        $character = StarWarsCharacter::first();

        $invalidData = [
            'first_name' => fake()->firstName(),
            'character' => $character->slug,
            'questions' => $differentQuestions->pluck('id')->toArray(),
            'responses' => [1, 2, 3, 4, 5],
        ];

        $response = $this->post(route('survey.store'), $invalidData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['questions']);

        $this->assertDatabaseMissing('survey_responses', [
            'first_name' => $invalidData['first_name'],
        ]);
    }

    public function test_survey_store_validates_required_fields(): void
    {
        $response = $this->post(route('survey.store'), []);

        $response->assertSessionHasErrors([
            'first_name',
            'character',
            'questions',
            'responses',
        ]);
    }

    public function test_survey_store_validates_array_sizes(): void
    {
        $questions = SurveyQuestion::limit(5)->get();
        session(['survey_questions' => $questions]);
        $character = StarWarsCharacter::first();

        $invalidData = [
            'first_name' => fake()->firstName(),
            'character' => $character->slug,
            'questions' => $questions->pluck('id')->take(3)->toArray(), // Only 3 questions
            'responses' => [1, 2, 3], // Only 3 responses
        ];

        $response = $this->post(route('survey.store'), $invalidData);

        $response->assertSessionHasErrors([
            'questions',
            'responses',
        ]);
    }

    public function test_survey_store_validates_response_range(): void
    {
        $questions = SurveyQuestion::limit(5)->get();
        session(['survey_questions' => $questions]);
        $character = StarWarsCharacter::first();

        $invalidData = [
            'first_name' => fake()->firstName(),
            'character' => $character->slug,
            'questions' => $questions->pluck('id')->toArray(),
            'responses' => [11, 0, -1, 15, 5], // Out of range values
        ];

        $response = $this->post(route('survey.store'), $invalidData);

        $response->assertSessionHasErrors(['responses.0', 'responses.1', 'responses.2', 'responses.3']);
    }

    public function test_survey_success_returns_inertia_response(): void
    {
        $response = $this->get(route('survey.success'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('SurveySuccess')
        );
    }

    public function test_survey_statistics_returns_correct_data(): void
    {
        // Create test data
        $questions = SurveyQuestion::factory()->count(3)->create();
        $characters = StarWarsCharacter::limit(2)->get();

        SurveyResponse::factory()->create([
            'character' => $characters[0]->slug,
            'questions' => $questions->pluck('id')->toArray(),
            'responses' => [8, 7, 9],
        ]);

        SurveyResponse::factory()->create([
            'character' => $characters[1]->slug,
            'questions' => $questions->pluck('id')->toArray(),
            'responses' => [6, 9, 7],
        ]);

        $response = $this->get(route('survey_statistics'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Survey/Statistics')
            ->has('statistics')
        );
    }

    public function test_survey_statistics_calculates_averages_correctly(): void
    {
        $question = SurveyQuestion::factory()->create();

        // Create responses that will give us predictable averages
        SurveyResponse::factory()->create([
            'questions' => [$question->id],
            'responses' => [5],
        ]);

        SurveyResponse::factory()->create([
            'questions' => [$question->id],
            'responses' => [8],
        ]);

        $response = $this->get(route('survey_statistics'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Survey/Statistics')
            ->has('statistics')
            ->where("statistics.{$question->id}.average_answer", 6.5)
            ->where("statistics.{$question->id}.total_responses", 2)
        );
    }

    public function test_character_statistics_returns_correct_data(): void
    {
        $questions = SurveyQuestion::factory()->count(2)->create();
        $characters = StarWarsCharacter::limit(2)->get();

        SurveyResponse::factory()->create([
            'character' => $characters[0]->slug,
            'questions' => $questions->pluck('id')->toArray(),
            'responses' => [8, 7],
        ]);

        SurveyResponse::factory()->create([
            'character' => $characters[1]->slug,
            'questions' => $questions->pluck('id')->toArray(),
            'responses' => [6, 9],
        ]);

        $response = $this->get(route('character_statistics', ['character' => $characters[0]->slug]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Survey/CharacterStatistics')
            ->has('statistics')
            ->where('character', $characters[0]->slug)
        );
    }

    public function test_character_statistics_filters_by_character(): void
    {
        $question = SurveyQuestion::factory()->create();
        $characters = StarWarsCharacter::limit(2)->get();

        SurveyResponse::factory()->create([
            'character' => $characters[0]->slug,
            'questions' => [$question->id],
            'responses' => [8],
        ]);

        SurveyResponse::factory()->create([
            'character' => $characters[1]->slug,
            'questions' => [$question->id],
            'responses' => [3],
        ]);

        $response = $this->get(route('character_statistics', ['character' => $characters[0]->slug]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Survey/CharacterStatistics')
            ->has('statistics')
            ->where('character', $characters[0]->slug)
            ->where("statistics.{$question->id}.average_answer", 8)
            ->where("statistics.{$question->id}.total_responses", 1)
        );
    }

    public function test_character_statistics_without_character_filter(): void
    {
        $question = SurveyQuestion::factory()->create();
        $characters = StarWarsCharacter::limit(2)->get();

        SurveyResponse::factory()->create([
            'character' => $characters[0]->slug,
            'questions' => [$question->id],
            'responses' => [8],
        ]);

        SurveyResponse::factory()->create([
            'character' => $characters[1]->slug,
            'questions' => [$question->id],
            'responses' => [4],
        ]);

        $response = $this->get(route('character_statistics'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Survey/CharacterStatistics')
            ->has('statistics')
            ->where("statistics.{$question->id}.average_answer", 6)
            ->where("statistics.{$question->id}.total_responses", 2)
        );
    }

    public function test_character_statistics_handles_missing_questions_gracefully(): void
    {
        $validQuestion = SurveyQuestion::factory()->create();
        $invalidQuestionId = 999; // Non-existent question ID
        $character = StarWarsCharacter::first();

        SurveyResponse::factory()->create([
            'character' => $character->slug,
            'questions' => [$validQuestion->id, $invalidQuestionId],
            'responses' => [8, 7],
        ]);

        $response = $this->get(route('character_statistics'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Survey/CharacterStatistics')
            ->has('statistics')
            ->has("statistics.{$validQuestion->id}")
            ->missing("statistics.{$invalidQuestionId}")
        );
    }

    public function test_get_characters_returns_json_response(): void
    {
        $response = $this->get(route('api.characters'));

        $response->assertStatus(200);
        $response->assertJsonCount(10);
    }

    public function test_statistics_handles_no_responses(): void
    {
        $question = SurveyQuestion::factory()->create();

        $response = $this->get(route('survey_statistics'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Survey/Statistics')
            ->has('statistics')
            ->where("statistics.{$question->id}.average_answer", 0)
            ->where("statistics.{$question->id}.total_responses", 0)
        );
    }

    public function test_character_statistics_handles_no_responses(): void
    {
        $question = SurveyQuestion::factory()->create();
        $character = StarWarsCharacter::first();

        $response = $this->get(route('character_statistics', ['character' => $character->slug]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Survey/CharacterStatistics')
            ->has('statistics')
            ->where('character', $character->slug)
        );
    }
}
