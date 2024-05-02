<?php

namespace App\Services\Interfaces;

use App\Dto\GameDto;
use App\Dto\GuessAttemptDto;

interface GameServiceInterface
{
    /**
     * Create a new game with the given game data
     *
     * @param GameDto $gameDto
     * @return int
     */
    public function createGame(GameDto $gameDto): int;

    /**
     * Add a proposal to the game with the given game ID and proposal
     *
     * @param int $gameId
     * @param string $proposal
     * @return GuessAttemptDto
     */
    public function addGuessAttempt(int $gameId, string $proposal): GuessAttemptDto;

    /**
     * Delete the game with the given ID and return the latest proposal data
     *
     * @param int $id
     * @return ?array
     */
    public function deleteGameById(int $id): ?array;
}
