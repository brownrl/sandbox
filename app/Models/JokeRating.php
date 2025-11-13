<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JokeRating extends Model
{
    /** @use HasFactory<\Database\Factories\JokeRatingFactory> */
    use HasFactory;

    protected $fillable = ['yo_momma_joke_id', 'llm_model_slug', 'rating'];

    public function joke(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(YoMommaJoke::class, 'yo_momma_joke_id');
    }
}
