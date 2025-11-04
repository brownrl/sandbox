<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlinkoGameRequest;
use App\Models\PlinkoGame;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PlinkoGameController extends Controller
{
    public function index(): Response
    {
        $recentGames = PlinkoGame::with('user:id,name')
            ->latest()
            ->limit(10)
            ->get(['id', 'user_id', 'score', 'drop_position', 'final_slot', 'created_at']);

        $highScores = PlinkoGame::with('user:id,name')
            ->orderByDesc('score')
            ->limit(10)
            ->get(['id', 'user_id', 'score', 'drop_position', 'final_slot', 'created_at']);

        $statistics = [
            'total_games' => PlinkoGame::count(),
            'total_winnings' => PlinkoGame::sum('score'),
            'average_score' => PlinkoGame::avg('score'),
            'highest_score' => PlinkoGame::max('score'),
        ];

        return Inertia::render('Plinko', [
            'recentGames' => $recentGames,
            'highScores' => $highScores,
            'statistics' => $statistics,
        ]);
    }

    public function store(StorePlinkoGameRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        PlinkoGame::create([
            'user_id' => auth()->id(),
            'score' => $validated['score'],
            'drop_position' => $validated['drop_position'],
            'final_slot' => $validated['final_slot'],
            'path' => $validated['path'] ?? null,
        ]);

        return redirect()->back();
    }
}
