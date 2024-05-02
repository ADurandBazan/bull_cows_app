<?php

namespace App\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="GuessAttemptDto",
 *     description="Data transfer object for a guess attempt",
 *     @OA\Property(
 *         property="attemptId",
 *         type="string",
 *         description="The ID of the guess attempt"
 *     ),
 *     @OA\Property(
 *         property="proposal",
 *         type="integer",
 *         description="The proposed number"
 *     ),
 *     @OA\Property(
 *         property="bull_count",
 *         type="integer",
 *         description="The number of bulls"
 *     ),
 *     @OA\Property(
 *         property="cows_count",
 *         type="integer",
 *         description="The number of cows"
 *     ),
 *     @OA\Property(
 *         property="attemps",
 *         type="integer",
 *         description="The number of attempts"
 *     ),
 *     @OA\Property(
 *         property="evaluation",
 *         type="number",
 *         format="float",
 *         description="The evaluation of the guess attempt"
 *     ),
 *     @OA\Property(
 *         property="ranking",
 *         type="integer",
 *         description="The ranking of the guess attempt"
 *     )
 * )
 */

class GuessAttemptDto
{
    protected $attemptId;
    protected $proposal;
    protected $bulls;
    protected $cows;
    protected $attemps;
    protected $evaluation;
    protected $ranking;

    public function __construct(string $attemptId,
        int $proposal,
        array $bulls = [],
        array $cows = [],
        int $attemps = 0,
        float $game_duration = 0,
        int $ranking = 0
    ) {
        $this->attemptId = $attemptId;
        $this->proposal = $proposal;
        $this->bulls = $bulls;
        $this->cows = $cows;
        $this->attemps = $attemps;
        $this->evaluation = $game_duration / 2 + $attemps;
        $this->ranking = $ranking;
    }

    public function toArray()
    {

        return [
            'attemptId' => $this->attemptId,
            'proposal' => $this->proposal,
            'result' => $this->getResultString(),
            'attemps' => $this->attemps,
            'evaluation' => $this->evaluation,
            'ranking' => $this->ranking,
            'win' => $this->isWin(),
        ];
    }

    private function getResultString(): string
    {
        if ($this->bulls['count'] == 4) {
            return 'Congratulations! You guessed the secret number.';
        }

        $bullsString = $this->bulls['count'] > 0 ? 'The bulls are ' . implode(',', $this->bulls['characters']) : 'No bulls.';
        $cowsString = $this->cows['count'] > 0 ? 'The cows are ' . implode(',', $this->cows['characters']) : 'No cows.';

        return 'There are ' . $this->bulls['count'] . ' bulls and ' . $this->cows['count'] . ' cows. ' . PHP_EOL . $bullsString . PHP_EOL . $cowsString;
    }

    public function isWin() : bool {
        return $this->bulls['count'] == 4;
    }
}
