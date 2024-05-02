<?php

namespace Tests\Unit;

use App\Dto\GameDto;
use App\Exceptions\GameAlreadyOverException;
use App\Exceptions\GameOverException;
use App\Models\Game;
use App\Repositories\Interfaces\GuessAttemptRepositoryInterface;
use App\Services\GameService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    protected $guessAttemptRepositoryMock;
    protected $gameService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guessAttemptRepositoryMock = Mockery::mock(GuessAttemptRepositoryInterface::class);
        $this->gameService = new GameService($this->guessAttemptRepositoryMock);
    }

    public function test_create_game()
    {
         // Create a new game and assert that the ID is returned
        $gameDto = new GameDto([
            'user' => "user1",
            'age' => 20,
        ]);
        $gameId = $this->gameService->createGame($gameDto);
        $this->assertIsInt($gameId);

        // Assert that a new game was created in the database
        $game = Game::find($gameId);
        $this->assertInstanceOf(Game::class, $game);
        $this->assertEquals($gameDto->secret_number, $game->secret_number);
    }

    public function test_delete_game_by_id()
    {
         $gameId = 999; // Assuming a non-existent game ID

        // Expecting a ModelNotFoundException when trying to delete a non-existent game
        $this->expectException(ModelNotFoundException::class);
        $latestGuessAttempt = $this->gameService->deleteGameById($gameId);
    }

    public function test_get_bulls()
    {
        $secretNumber = '1234';
        $inputNumber = '1436';
        $result = $this->gameService->getBulls($secretNumber, $inputNumber);
        $this->assertEquals(['count' => 2, 'characters' => ['1', '3']], $result);
    }

    public function test_get_cows()
    {
         $secretNumber = '1234';
        $inputNumber = '1436';
        $result = $this->gameService->getCows($secretNumber, $inputNumber);
        $this->assertEquals(['count' => 1, 'characters' => ['4']], $result);
    }

    public function test_winned_game_already_over()
    {
        // Create a sample game that is winned for testing
        $game = Game::create([
            'expires_at' => '2022-12-31 23:59:59',
            'secret_number' => '1234',
            'win' => true,
            'lose' => false,
            'attempts_count' => 0,
            'user' => 'user1',
            'evaluation' => 0,
            'age' => 15,
        ]);
        $guessAttempts = []; // Empty array of guess attempts
        $this->guessAttemptRepositoryMock->shouldReceive('getGuessAttemptsByGameId')
            ->with($game->id)
            ->andReturn($guessAttempts);

        // Attempt to add a guess attempt to the game
        $this->expectException(GameAlreadyOverException::class);
        $this->gameService->addGuessAttempt($game->id, '1234');
    }

    public function test_lossed_game_already_over()
    {
        // Create a sample game that is losed for testing
        $game = Game::create([
            'expires_at' => '2022-12-31 23:59:59',
            'secret_number' => '1234',
            'win' => false,
            'lose' => true,
            'attempts_count' => 0,
            'user' => 'user1',
            'evaluation' => 0,
            'age' => 15,
        ]);
        $guessAttempts = []; // Empty array of guess attempts
        $this->guessAttemptRepositoryMock->shouldReceive('getGuessAttemptsByGameId')
            ->with($game->id)
            ->andReturn($guessAttempts);

        // Attempt to add a guess attempt to the game
        $this->expectException(GameAlreadyOverException::class);
        $this->gameService->addGuessAttempt($game->id, '1234');
    }

    public function test_game_over()
    {
        // Create a game that is expired (you may need to adjust the expiration time based on your implementation)
        $game = Game::create([
            'expires_at' => '2022-12-31 23:59:59',
            'secret_number' => '1234',
            'win' => false,
            'lose' => false,
            'attempts_count' => 0,
            'user' => 'user1',
            'evaluation' => 0,
            'age' => 15,
        ]);
        $guessAttempts = []; // Empty array of guess attempts
        $this->guessAttemptRepositoryMock->shouldReceive('getGuessAttemptsByGameId')
            ->with($game->id)
            ->andReturn($guessAttempts);
        $this->guessAttemptRepositoryMock->shouldReceive('removeGuessAttemptByGameId')
            ->with($game->id);

        // Attempt to add a guess attempt to the game
        $this->expectException(GameOverException::class);
        $this->gameService->addGuessAttempt($game->id, '5678');
    }
}
