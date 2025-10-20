<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SurveyQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            'The Force is stronger with those who have had their morning coffee.',
            'Ewoks would make excellent pets in real life.',
            'The Death Star was actually an innovative architectural achievement.',
            'Jar Jar Binks deserved more screen time in the prequel trilogy.',
            'Wookiees have the best hair care routine in the galaxy.',
            'The Empire did nothing wrong and brought order to the galaxy.',
            'Yoda\'s backwards speech pattern would be annoying in daily conversation.',
            'Han Solo shot first because he\'s simply a better marksman.',
            'The Jedi Council needed better conflict resolution training.',
            'Darth Vader\'s breathing would be helpful for meditation apps.',
            'C-3PO is the most relatable character in the Star Wars universe.',
            'The cantina band plays music that would top modern charts.',
            'Lightsabers are impractical weapons for everyday self-defense.',
            'Princess Leia\'s bun hairstyle should make a fashion comeback.',
            'The Millennium Falcon needs a serious interior decorator.',
            'Stormtroopers actually have excellent aim when it matters.',
            'Tatooine would be a terrible vacation destination due to the twin suns.',
            'R2-D2\'s beeping is more eloquent than most political speeches.',
            'The Sarlacc pit would make an interesting extreme sports venue.',
            'Boba Fett\'s jetpack is the ultimate commuter transportation solution.',
        ];

        foreach ($questions as $question) {
            \App\Models\SurveyQuestion::create([
                'question' => $question,
                'is_active' => true,
            ]);
        }
    }
}
