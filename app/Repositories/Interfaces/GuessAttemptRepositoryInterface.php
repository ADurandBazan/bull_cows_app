<?php

namespace App\Repositories\Interfaces;

use App\Dto\GuessAttemptDto;

interface GuessAttemptRepositoryInterface
{
    // Store a proposal with the given game ID, proposal, and available time
    public function storeGuessAttempt(int $gameId, GuessAttemptDto $proposal, int $availableTime): void;

    // Get all proposals for the game with the given game ID
    public function getGuessAttemptsByGameId(string $gameId): array;

    // Remove all proposals for the game with the given game ID
    public function removeGuessAttemptByGameId(string $gameId): void;
}