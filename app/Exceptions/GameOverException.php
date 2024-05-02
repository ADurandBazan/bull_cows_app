<?php

namespace App\Exceptions;

use Exception;

class GameOverException extends Exception
{
    public $secret_number;
    public function __construct($secret_number)
    {
        $this->secret_number = $secret_number;
        parent::__construct('Sorry timeout. Game Over!');
    }
}