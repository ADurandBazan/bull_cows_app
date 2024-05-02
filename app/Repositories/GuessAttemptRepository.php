<?php

namespace App\Repositories;

use App\Repositories\Interfaces\GuessAttemptRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use App\Dto\GuessAttemptDto;

class GuessAttemptRepository implements GuessAttemptRepositoryInterface
{
    // Store a proposal with the given game ID, proposal, and available time
    public function storeGuessAttempt(int $gameId, GuessAttemptDto $proposal, int $availableTime): void
    {

        // Get the data from cache or initialize it as an empty array
        $data = Cache::get($gameId, []);

        // Add the proposal to the data array
        $data[] = $proposal;

        // Store the data in cache with the given available time
        Cache::put($gameId, $data, $availableTime);
    }

    // Get all proposals for the game with the given game ID
    public function getGuessAttemptsByGameId(string $gameId): array
    {
        // Get the data from cache or return an empty array
        return Cache::get($gameId, []);
    }

    // Remove all proposals for the game with the given game ID
    public function removeGuessAttemptByGameId(string $gameId): void
    {
        // Forget the data from cache
        Cache::forget($gameId);
    }
}