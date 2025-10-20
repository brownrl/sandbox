<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    /** @use HasFactory<\Database\Factories\SurveyResponseFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'character',
        'questions',
        'responses',
    ];

    protected function casts(): array
    {
        return [
            'questions' => 'array',
            'responses' => 'array',
        ];
    }
}
