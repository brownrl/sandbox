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
            ->get(['id', 'user_id', 'score', 'final_slot', 'created_at']);

        $highScores = PlinkoGame::with('user:id,name')
            ->orderByDesc('score')
            ->limit(10)
            ->get(['id', 'user_id', 'score', 'final_slot', 'created_at']);

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
            'final_slot' => $validated['final_slot'],
            'drop_x' => $validated['drop_x'],
            'final_x' => $validated['final_x'],
            'horizontal_distance' => $validated['horizontal_distance'],
            'path' => $validated['path'],
            'fall_time_ms' => $validated['fall_time_ms'],
            'peg_collisions' => $validated['peg_collisions'],
        ]);

        return redirect()->back();
    }

    public function deepDive(): Response
    {
        $games = PlinkoGame::all();
        $all_paths = $games->pluck('path')->filter()->flatten(1);

        $horizontal_distances = $games->pluck('horizontal_distance')->filter();
        $buckets = [];

        if ($horizontal_distances->isNotEmpty()) {
            $max_distance = $horizontal_distances->max();
            $min_distance = $horizontal_distances->min();

            if ($max_distance === $min_distance) {
                // Handle case where all distances are the same
                $buckets[sprintf('%.2f', $min_distance)] = $horizontal_distances->count();
            } else {
                $bucket_size = ($max_distance - $min_distance) / 10;
                $buckets = collect(range(0, 9))->reduce(function ($carry, $i) use ($min_distance, $bucket_size) {
                    $start = $min_distance + ($i * $bucket_size);
                    $end = $start + $bucket_size;
                    $carry[sprintf('%.2f-%.2f', $start, $end)] = 0;
                    return $carry;
                }, []);

                foreach ($horizontal_distances as $distance) {
                    foreach ($buckets as $range => $count) {
                        [$start, $end] = explode('-', $range);
                        if ($distance >= $start && $distance < $end) {
                            $buckets[$range]++;
                            break;
                        }
                    }
                }
            }
        }

        $statistics = [
            'total_games' => $games->count(),
            'total_winnings' => $games->sum('score'),
            'average_score' => $games->avg('score'),
            'highest_score' => $games->max('score'),
            'landing_slot_distribution' => $games->groupBy('final_slot')->map->count(),
            'average_horizontal_distance' => $games->avg('horizontal_distance'),
            'horizontal_distance_histogram' => $buckets,
            'average_fall_time_ms' => $games->avg('fall_time_ms'),
            'average_peg_collisions' => $games->avg('peg_collisions'),
            'all_paths' => PlinkoGame::latest()->limit(100)->pluck('path')->filter()->flatten(1),
        ];

        return Inertia::render('PlinkoDeepDive', [
            'statistics' => $statistics,
        ]);
    }
}
