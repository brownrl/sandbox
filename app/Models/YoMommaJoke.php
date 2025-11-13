<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoMommaJoke extends Model
{
    /** @use HasFactory<\Database\Factories\YoMommaJokeFactory> */
    use HasFactory;

    protected $fillable = ['joke'];

    public function ratings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JokeRating::class);
    }
}
