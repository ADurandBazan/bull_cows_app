<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'secret_number',
        'user',
        'age',
        'attempts_count',
        'win',
        'lose',
        'evaluation',
        'expires_at',
    ];

    /**
     * Check if the game is over (win or lose)
     *
     * @return bool
     */
    public function isOver(): bool
    {
        return $this->win || $this->lose;
    }

    /**
     * Check if the game has expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return !is_null($this->expires_at) && now()->gt($this->expires_at);
    }

    /**
     * Get the game duration in seconds
     *
     * @return float
     */
    public function getDuration(): float
    {
        return now()->gt($this->expires_at) ? 0 :
        Carbon::parse($this->expires_at)->diffInSeconds(now());
    }

    /**
     * Calculate the evaluation of the game based on the number of bulls and cows
     *
     * @return void
     */
    public function calculateEvaluation(): void
    {
        $this->evaluation = $this->getDuration() / 2 + $this->attempts_count;
    }
}
