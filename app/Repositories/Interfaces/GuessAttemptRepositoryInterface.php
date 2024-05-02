<?php

namespace App\Repositories\Interfaces;

use App\Dto\GuessAttemptDto;

interface GuessAttemptRepositoryInterface
{
    /**
     * Store a proposal with the given game ID, proposal, and available time
     *
     * @param int $gameId
     * @param GuessAttemptDto $proposal
     * @param int $availableTime
     * @return void
     */
    public function storeGuessAttempt(int $gameId, GuessAttemptDto $proposal, int $availableTime): void;

    /**
     * Get all proposals for the game with the given game ID
     *
     * @param string $gameId
     * @return array
     */
    public function getGuessAttemptsByGameId(string $gameId): array;

    /**
     * Remove all proposals for the game with the given game ID
     *
     * @param string $gameId
     * @return void
     */
    public function removeGuessAttemptByGameId(string $gameId): void;
}
