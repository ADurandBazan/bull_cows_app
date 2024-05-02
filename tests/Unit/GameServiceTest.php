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
         // Create a new game using the factory
    $game = Game::factory()->create([
        'secret_number' => 1234,
        
    ]);

    // Assert that a new game was created in the database
    $this->assertDatabaseHas('games', [
        'secret_number' => 1234,
    ]);

    // Assert that the created game matches the expected attributes
    $this->assertInstanceOf(Game::class, $game);
    $this->assertEquals($game->secret_number, 1234);
   
    }

    public function test_delete_game_by_id()
    {
        // Create a game using the factory
        $game = Game::factory()->create();
    
        //ID that there is not exist
        $gameId = 999;

        $guessAttempts = []; // Empty array of guess attempts
       
        // Expecting a ModelNotFoundException when trying to delete a non-existent game
        $this->expectException(ModelNotFoundException::class);

        $this->guessAttemptRepositoryMock->shouldReceive('getGuessAttemptsByGameId')
        ->with($game->id)
        ->andReturn($guessAttempts);

    
        // Simulate the deletion of the game with id that there is not exist
        $this->gameService->deleteGameById($gameId);
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
        // Create a sample game that is winned for testing using the factory
        $game = Game::factory()->create([
            'win' => true,
            'lose' => false,
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
        $game = Game::factory()->create([
            'win' => false,
            'lose' => true,
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
        $game = Game::factory()->create([
            'expires_at' => '2022-12-31 23:59:59',
            'win' => false,
            'lose' => false,
           
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
