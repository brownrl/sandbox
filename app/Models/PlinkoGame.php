<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlinkoGame extends Model
{
    /** @use HasFactory<\Database\Factories\PlinkoGameFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'score',
        'drop_position',
        'final_slot',
        'path',
    ];

    protected function casts(): array
    {
        return [
            'path' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
