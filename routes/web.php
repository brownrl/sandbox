<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('clock', function () {
    return Inertia::render('Clock');
})->name('clock');

Route::get('ball', function () {
    return Inertia::render('Ball');
})->name('ball');

Route::get('puzzle', function () {
    return Inertia::render('Puzzle');
})->name('puzzle');

Route::get('snakedo', function () {
    return Inertia::render('Snakedo');
})->name('snakedo');

Route::get('a-star', function () {
    return Inertia::render('AStar');
})->name('a-star');

Route::get('le-ball', App\Http\Controllers\LeBallController::class)->name('le-ball');

Route::get('survey', [App\Http\Controllers\SurveyController::class, 'show'])->name('survey');
Route::post('survey', [App\Http\Controllers\SurveyController::class, 'store'])->name('survey.store');
Route::get('survey-success', [App\Http\Controllers\SurveyController::class, 'success'])->name('survey.success');
Route::get('survey-statistics', [App\Http\Controllers\SurveyController::class, 'statistics'])->name('survey_statistics');
Route::get('character-statistics', [App\Http\Controllers\SurveyController::class, 'characterStatistics'])->name('character_statistics');

// Debug route to clear survey session (remove in production)
Route::get('survey/reset', function () {
    session()->forget('survey_questions');

    return redirect()->route('survey')->with('message', 'Survey questions reset');
});

Route::get('jokes', function () {
    $joke = App\Models\OmarJoke::inRandomOrder()->first();

    return Inertia::render('Jokes', [
        'joke' => $joke ? $joke->joke : 'No jokes found, sorry!',
    ]);
})->name('jokes');

Route::get('plinko', [App\Http\Controllers\PlinkoGameController::class, 'index'])->name('plinko');
Route::post('plinko', [App\Http\Controllers\PlinkoGameController::class, 'store'])->name('plinko.store');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
