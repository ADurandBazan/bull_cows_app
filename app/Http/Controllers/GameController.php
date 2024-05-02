<?php

namespace App\Http\Controllers;

use App\Dto\GameDto;
use App\Dto\GuessAttemptDto;
use App\Exceptions\DuplicateProposalException;
use App\Exceptions\GameOverException;
use App\Exceptions\GameAlreadyOverException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\GuessAttemptRequest;
use App\Http\Requests\StoreGameRequest;
use App\Services\Interfaces\GameServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class GameController extends Controller
{
    // Inject the game service in the constructor
    protected $gameService;

    public function __construct(GameServiceInterface $gameService)
    {
        $this->gameService = $gameService;
    }

    // Store a new game with the given validated data
    /**
     * @OA\Post(
     *     path="/api/game",
     *     summary="Create a new game",
     *     description="Create a new game with the given validated data",
     *     tags={"game"},
     *     @OA\RequestBody(
     *         description="Game data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreGameRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Game created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="gameId", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function store(StoreGameRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        if (empty($validatedData)) {
            return response()->json($request->errors(), 422);
        }

        $id = $this->gameService->createGame(new GameDto($validatedData));

        return response()->json(['gameId' => $id], 201);
    }

    // Add a new proposal to the game with the given ID and validated data
    /**
     * @OA\Post(
     *     path="/api/game/{id}/attempt",
     *     summary="Add a new attempt to the game",
     *     description="Add a new attempt to the game with the given validated data",
     *     tags={"game"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the game",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Attempt data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GuessAttemptRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attempt added successfully",
     *         @OA\JsonContent(ref="#/components/schemas/GuessAttemptDto")
     *     ),
     *     @OA\Response(
     *         response=412,
     *         description="Duplicate proposal error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string")
     *         )
     *     ),
     * @OA\Response(
     *         response=410,
     *         description="Game already over error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=408,
     *         description="Game over error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="secret_number", type="integer")
     *         )
     *     ),
     *  @OA\Response(
     *         response=404,
     *         description="Element not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string")
     *         )
     *     )
     * )
     */
    public function addAttempt(GuessAttemptRequest $request, int $id): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            // Call the game service to add the proposal and handle exceptions
            $response = $this->gameService->addGuessAttempt($id, $validatedData['proposal']);

        } catch (DuplicateProposalException $ex) {
            return response()->json(['errors' => $ex->getMessage()], 412);

        } catch (GameAlreadyOverException $ex) {
            return response()->json(['errors' => $ex->getMessage()], 410);
        }
        
        catch (GameOverException $ex) {
            return response()->json([
                'error' => $ex->getMessage(),
                'secret_number' => $ex->secret_number,

            ], 408);

        } catch (ModelNotFoundException $e) {
            return response()->json([
               'error' => 'Element not found'
             ], 404);

        } catch (Exception $ex) {
            return response()->json(['errors' => $ex->getMessage()], 500);
        }

        return response()->json($response->toArray(), 200);
    }

    // Delete the game with the given ID and return the latest proposal data
    /**
     * @OA\Delete(
     *     path="/api/game/{id}",
     *     summary="Delete a game",
     *     description="Delete a game with the given ID",
     *     tags={"game"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the game",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Game deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/GuessAttemptDto")
     *     ),
     *  @OA\Response(
     *         response=404,
     *         description="Element not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string")
     *         )
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        try {
            // Call the game service to delete the game and handle exceptions
            $response = $this->gameService->deleteGameById($id);

            return response()->json($response, 200);

        } catch (ModelNotFoundException $e) {
             return response()->json([
                'error' => 'Element not found'
              ], 404);
        } 
        
        catch (Exception $ex) {
            return response()->json(['errors' => $ex->getMessage()], 500);
        }
    }
}
