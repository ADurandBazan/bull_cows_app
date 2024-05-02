<?php

namespace App\Exceptions;

use Exception;

class GameAlreadyOverException extends Exception
{
    public function __construct($gameId)
    {
        parent::__construct("The game with id ". $gameId ." is already over.");
    }
}