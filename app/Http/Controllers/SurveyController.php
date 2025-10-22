<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
use App\Models\StarWarsCharacter;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SurveyController extends Controller
{
    public function show(): Response
    {
        // Check if we already have questions in session
        if (! session()->has('survey_questions')) {
            // Get 5 random questions from the pool of 20 and store in session
            $questions = SurveyQuestion::where('is_active', true)
                ->inRandomOrder()
                ->limit(5)
                ->get(['id', 'question']);

            session(['survey_questions' => $questions]);
        }

        $questions = session('survey_questions');

        // Get all characters from the database
        $characters = StarWarsCharacter::select('name as label', 'slug as value', 'description')
            ->orderBy('name')
            ->get();

        return Inertia::render('Survey', [
            'questions' => $questions,
            'characters' => $characters,
        ]);
    }

    public function store(StoreSurveyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Verify the submitted questions match the session questions
        $sessionQuestions = session('survey_questions');
        $sessionQuestionIds = $sessionQuestions->pluck('id')->toArray();

        if (array_diff($validated['questions'], $sessionQuestionIds) ||
            array_diff($sessionQuestionIds, $validated['questions'])) {
            return redirect()->back()->withErrors(['questions' => 'Invalid questions submitted.']);
        }

        SurveyResponse::create([
            'first_name' => $validated['first_name'],
            'character' => $validated['character'],
            'questions' => $validated['questions'],
            'responses' => $validated['responses'],
        ]);

        // Clear the session questions after successful submission
        session()->forget('survey_questions');

        return redirect()->route('survey.success');
    }

    public function success(): Response
    {
        return Inertia::render('SurveySuccess');
    }

    public function statistics(): Response
    {
        $responses = SurveyResponse::all();
        $questions = SurveyQuestion::all()->keyBy('id');

        $statistics = [];
        foreach ($questions as $question) {
            $statistics[$question->id] = [
                'question' => $question->question,
                'character_counts' => [],
                'response_counts' => array_fill(1, 10, 0),
            ];
        }

        $global_statistics = [];
        $global_statistics['total_responses'] = $responses->count();

        foreach ($responses as $response) {
            foreach ($response->questions as $index => $questionId) {
                $character = $response->character;
                $rating = $response->responses[$index];

                if (! isset($statistics[$questionId]['character_counts'][$character])) {
                    $statistics[$questionId]['character_counts'][$character] = 0;
                }
                $statistics[$questionId]['character_counts'][$character]++;

                if (isset($statistics[$questionId]['response_counts'][$rating])) {
                    $statistics[$questionId]['response_counts'][$rating]++;
                }
            }
        }

        // Find the most chosen character for each question
        foreach ($statistics as $questionId => &$stat) {
            arsort($stat['character_counts']);
            $stat['most_chosen_character'] = key($stat['character_counts']);

            // Calculate the mode (most common answer)
            $response_counts = $stat['response_counts'];
            arsort($response_counts);
            $stat['most_common_answer'] = key($response_counts);

            // Calculate the average answer and total responses
            $total_responses = array_sum($stat['response_counts']);
            $stat['total_responses'] = $total_responses;

            if ($total_responses > 0) {
                $weighted_sum = 0;
                foreach ($stat['response_counts'] as $rating => $count) {
                    $weighted_sum += $rating * $count;
                }
                $stat['average_answer'] = round($weighted_sum / $total_responses, 1);
            } else {
                $stat['average_answer'] = 0;
            }
        }

        // Get all characters for label resolution
        $characters = StarWarsCharacter::select('name as label', 'slug as value', 'description')
            ->orderBy('name')
            ->get();

        $global_statistics['most_popular_character_overall'] = SurveyResponse::select('character')
            ->groupBy('character')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(1)
            ->pluck('character')
            ->first();

        return Inertia::render('Survey/Statistics', [
            'statistics' => $statistics,
            'characters' => $characters,
            'global_statistics' => $global_statistics,
        ]);
    }

    public function characterStatistics(): Response
    {
        $character = request('character');
        $responses = SurveyResponse::when($character, fn ($query) => $query->where('character', $character))->get();
        $questions = SurveyQuestion::all()->keyBy('id');

        $statistics = [];

        foreach ($responses as $response) {
            foreach ($response->questions as $index => $questionId) {
                if (! isset($questions[$questionId])) {
                    continue;
                }

                if (! isset($statistics[$questionId])) {
                    $statistics[$questionId] = [
                        'question' => $questions[$questionId]->question,
                        'response_counts' => array_fill(1, 10, 0),
                    ];
                }

                $rating = $response->responses[$index];

                if (isset($statistics[$questionId]['response_counts'][$rating])) {
                    $statistics[$questionId]['response_counts'][$rating]++;
                }
            }
        }

        foreach ($statistics as $questionId => &$stat) {
            $response_counts = $stat['response_counts'];
            arsort($response_counts);
            $stat['most_common_answer'] = key($response_counts);

            $total_responses = array_sum($stat['response_counts']);
            $stat['total_responses'] = $total_responses;

            if ($total_responses > 0) {
                $weighted_sum = 0;
                foreach ($stat['response_counts'] as $rating => $count) {
                    $weighted_sum += $rating * $count;
                }
                $stat['average_answer'] = round($weighted_sum / $total_responses, 1);
            } else {
                $stat['average_answer'] = 0;
            }
        }

        // Get all characters for selection
        $characters = StarWarsCharacter::select('name as label', 'slug as value', 'description')
            ->orderBy('name')
            ->get();

        return Inertia::render('Survey/CharacterStatistics', [
            'statistics' => $statistics,
            'character' => $character,
            'characters' => $characters,
        ]);
    }

    public function getCharacters(): JsonResponse
    {
        $characters = StarWarsCharacter::select('name as label', 'slug as value', 'description')
            ->orderBy('name')
            ->get();

        return response()->json($characters);
    }
}
