<?php

namespace App\Dto;

class GameDto
{
    public $user;
    public $secret_number;
    public $age;
    public $win = false;
    public $lose = false;
    public $attempts_count = 0;
    public $expires_at;
    public $evaluation = 0;

    public function __construct(array $data)
    {
        $this->user = $data['user'];
        $this->secret_number = $this->generateSecretNumber();
        $this->age = $data['age'];
        $this->expires_at = now()->addSeconds(env('MAX_GAME_ACTIVE_TIME'));

    }

    public function toArray()
    {
        return [
            'user' => $this->user,
            'secret_number' => $this->secret_number,
            'age' => $this->age,
            'win' => $this->win,
            'lose' => $this->lose,
            'attempts_count' => $this->attempts_count,
            'evaluation' => $this->evaluation,
            'expires_at' => $this->expires_at,
        ];
    }

    /**
     * Generate a secret number for a new game
     *
     * @return string
     */
    private function generateSecretNumber()
    {
        while (true) {
            $number = rand(1000, 9999);
            if (count(array_unique(str_split($number))) == 4) {
                return $number;
            }
        }
    }
}
