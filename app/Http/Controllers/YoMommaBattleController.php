<?php

namespace App\Http\Controllers;

use App\Models\JokeRating;
use App\Models\YoMommaJoke;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class YoMommaBattleController extends Controller
{
    public function index(): Response
    {
        $llmModels = [
            ['name' => 'GPT-4o', 'slug' => 'gpt-4o'],
            ['name' => 'Claude 3.5 Sonnet', 'slug' => 'claude-3.5-sonnet'],
            ['name' => 'Gemini 2.0 Flash', 'slug' => 'gemini-2.0-flash'],
            ['name' => 'Llama 3.3 70B', 'slug' => 'llama-3.3-70b'],
            ['name' => 'Grok 2', 'slug' => 'grok-2'],
        ];

        return Inertia::render('YoMommaBattle', [
            'llmModels' => $llmModels,
        ]);
    }

    public function getRandomJoke(): \Illuminate\Http\JsonResponse
    {
        $joke = YoMommaJoke::inRandomOrder()->first();

        return response()->json([
            'id' => $joke->id,
            'joke' => $joke->joke,
        ]);
    }

    public function rateJoke(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'joke_id' => 'required|exists:yo_momma_jokes,id',
            'llm_model_slug' => 'required|string',
            'rating' => 'required|integer|min:0|max:5',
        ]);

        JokeRating::create([
            'yo_momma_joke_id' => $validated['joke_id'],
            'llm_model_slug' => $validated['llm_model_slug'],
            'rating' => $validated['rating'],
        ]);

        return response()->json(['success' => true]);
    }
}
