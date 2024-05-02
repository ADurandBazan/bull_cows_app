<?php

namespace App\Services;

use App\Dto\GameDto;
use App\Dto\GuessAttemptDto;
use App\Exceptions\DuplicateProposalException;
use App\Exceptions\GameAlreadyOverException;
use App\Exceptions\GameOverException;
use App\Models\Game;
use App\Repositories\Interfaces\GuessAttemptRepositoryInterface;
use App\Services\Interfaces\GameServiceInterface;
use Carbon\Carbon;

class GameService implements GameServiceInterface
{
    protected $proposalRepository;

    public function __construct(GuessAttemptRepositoryInterface $proposalRepository)
    {
        $this->proposalRepository = $proposalRepository;
    }

    /**
     * Create a new game and return its ID
     *
     * @param GameDto $gameDto
     * @return int
     */
    public function createGame(GameDto $gameDto): int
    {
        $game = Game::create($gameDto->toArray());
        $game->save();

        return $game->id;
    }

    /**
     * Add a proposal to a game and return the updated game data
     *
     * @param int $gameId
     * @param string $proposal
     * @return GuessAttemptDto
     */
    public function addGuessAttempt(int $gameId, string $proposal): GuessAttemptDto
    {
        // Throw an exception if the proposal already exists for the game
        if ($this->containsGuessAttempt($gameId, $proposal)) {
            throw new DuplicateProposalException();
        }

        // Find the game or throw an exception if it doesn't exist
        $game = Game::findOrFail($gameId);

        // Throw an exception if the game is already over
        if ($game->isOver()) {
            throw new GameAlreadyOverException($gameId);
        }

        // Calculate the remaining time for the game
        $now = Carbon::now();
        $gameExpiresAt = Carbon::parse($game->expires_at);
        $availableTime = $now->diffInSeconds($gameExpiresAt);

        // Check if the game has expired and update its status if necessary
        if ($game->isExpired()) {
            $game->lose = true;
            $game->save();

            // Remove all proposals for the current game from cache
            $this->proposalRepository->removeGuessAttemptByGameId($gameId);

            throw new GameOverException($game->secret_number);
        }

        // Check if the proposal is correct and update the game status if necessary
        $secretNumber = $game->secret_number;
        if ($secretNumber === $proposal) {
            $game->win = true;
        }

        // Update the game attempts count and save it
        $game->increment('attempts_count');
        $game->calculateEvaluation();
        $game->save();

        // Calculate the game ranking and bulls and cows for the proposal
        $bulls = $this->getBulls($secretNumber, $proposal);
        $cows = $this->getCows($secretNumber, $proposal);
        $gamesRanking = $this->calculateRanking($game);

        // Create a new proposal data object and store it in cache
        $attemptData = new GuessAttemptDto(
            $game->attempts_count,
            $proposal,
            $bulls,
            $cows,
            $game->attempts_count,
            $game->evaluation,
            $gamesRanking
        );
        $this->proposalRepository->storeGuessAttempt($gameId, $attemptData, $availableTime);

        // Return the updated game data
        return $attemptData;
    }

    /**
     * Delete a game and return the latest proposal data
     *
     * @param int $id
     * @return ?array
     */
    public function deleteGameById(int $id): ?array
    {
        // Find the game or throw an exception if it doesn't exist
        $game = Game::findOrFail($id);
        $game->delete();

        // Get all proposals for the game from cache
        $proposals = $this->proposalRepository->getGuessAttemptsByGameId($id);

        // Get the latest proposal data and remove all proposals from cache
        $latestGuessAttempt = null;
        if ($proposals != null) {
            // Getting the last proposal
            $maxId = 0;
            foreach ($proposals as $element) {
                $array = $element->toArray();

                if ($array['attemptId'] > $maxId) {
                    $maxId = $array['attemptId'];
                    $latestGuessAttempt = $array;
                }
            }

            // Removing proposals from cache
            $this->proposalRepository->removeGuessAttemptByGameId($id);
        }

        return $latestGuessAttempt;
    }

    /**
     * Check if a proposal already exists for a game
     *
     * @param string $gameId
     * @param string $proposal
     * @return bool
     */
    private function containsGuessAttempt(string $gameId, string $proposal): bool
    {
        $proposals = $this->proposalRepository->getGuessAttemptsByGameId($gameId);

        if ($proposals == null) {
            return false;
        }

        foreach ($proposals as $element) {

            $array = $element->toArray();

            if ($array['proposal'] == $proposal) {

                return true;
            }
        }

        return false;
    }

    /**
     * Calculate the number of bulls (correct digits in the right positions)
     *
     * @param string $secretstring
     * @param string $inputstring
     * @return array
     */
    public function getBulls(string $secretstring, string $inputstring): array
    {
        $len = min(strlen($secretstring), strlen($inputstring));
        $bulls_count = 0;
        $bulls_chars = [];

        for ($i = 0; $i < $len; $i++) {
            $subStr1 = substr($secretstring, $i, 1);
            $subStr2 = substr($inputstring, $i, 1);

            if ($subStr1 === $subStr2) {
                $bulls_count++;
                $bulls_chars[] = $subStr1;
            }
        }

        return [
            'count' => $bulls_count,
            'characters' => $bulls_chars,
        ];
    }

    /**
     * Calculate the number of cows (correct digits in the wrong positions)
     *
     * @param string $secretString
     * @param string $inputString
     * @return array
     */
    public function getCows(string $secretString, string $inputString): array
    {
        $cows_count = 0;
        $cows_chars = [];
        $secretStringLength = strlen($secretString);

        for ($i = 0; $i < $secretStringLength; $i++) {
            $secretChar = $secretString[$i];
            for ($j = 0; $j < $secretStringLength; $j++) {
                if ($i != $j && $secretChar == $inputString[$j]) {
                    if (!in_array($secretChar, $cows_chars)) {
                        $cows_chars[] = $secretChar;
                    }
                    $cows_count++;
                    break;
                }
            }
        }

        return [
            'count' => $cows_count,
            'characters' => $cows_chars,
        ];
    }

    /**
     * Calculate the ranking of the game based on the number of winning games and the evaluation of non-winning games
     *
     * @param Game $game
     * @return int
     */
    public function calculateRanking(Game $game): int
    {
        $gamesRanking = Game::where('win', true)->count();

        $notWinningGames = Game::where('win', false)
            ->orderBy('evaluation', 'asc')
            ->get();

        foreach ($notWinningGames as $element) {
            $gamesRanking++;

            if ($element->id == $game->id) {
                return $gamesRanking;
            }
        }

        return 0;
    }

}
