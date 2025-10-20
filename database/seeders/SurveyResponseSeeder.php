<?php

namespace Database\Seeders;

use App\Models\StarWarsCharacter;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Database\Seeder;

class SurveyResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = SurveyQuestion::where('is_active', true)->get();
        $characterIds = StarWarsCharacter::pluck('slug')->toArray();

        for ($i = 0; $i < 200; $i++) {
            $selectedQuestions = $questions->random(5);
            $responses = [];
            foreach ($selectedQuestions as $question) {
                $responses[] = rand(1, 10);
            }

            SurveyResponse::create([
                'first_name' => fake()->firstName(),
                'character' => $characterIds[array_rand($characterIds)],
                'questions' => $selectedQuestions->pluck('id')->toArray(),
                'responses' => $responses,
            ]);
        }
    }
}