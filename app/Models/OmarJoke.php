<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmarJoke extends Model
{
    /** @use HasFactory<\Database\Factories\OmarJokeFactory> */
    use HasFactory;

    protected $fillable = ['joke'];
}
