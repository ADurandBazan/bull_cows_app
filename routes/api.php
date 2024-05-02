<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Middleware\HttpsProtocol;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('game')->middleware([HttpsProtocol::class])->group(function () {
    Route::post('', [GameController::class, 'store'])->middleware('throttle:5,1');
    Route::post('{id}/attempt', [GameController::class, 'addAttempt'])->middleware('throttle:60,1');
    Route::delete('{id}', [GameController::class, 'delete']);
});