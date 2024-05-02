<?php

namespace App\Models;

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
        'expires_at',
    ];

    public function isOver(): bool
    {
        return $this->win || $this->lose;
    }

    public function isExpired(): bool
    {
        return !is_null($this->expires_at) && now()->gt($this->expires_at);
    }
}
