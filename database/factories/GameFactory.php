<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition()
    {
        return [
            'secret_number' => $this->faker->randomNumber(4),
            'user' => $this->faker->name,
            'age' => $this->faker->numberBetween(18, 60),
            'evaluation' => $this->faker->numberBetween(1, 100),
            'attempts_count' => $this->faker->numberBetween(1, 100),
            'win' => $this->faker->boolean,
            'lose' => $this->faker->boolean,
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 hour'),
        ];
    }
}