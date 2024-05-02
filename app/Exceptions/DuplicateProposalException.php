<?php

namespace App\Exceptions;

use Exception;

class DuplicateProposalException extends Exception
{
    public function __construct($message = 'Duplicate proposal')
    {
        parent::__construct($message);
    }
}