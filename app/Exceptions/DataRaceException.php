<?php

namespace App\Exceptions;

class DataRaceException extends \Exception
{
    public function __construct(protected $message = '')
    {
        parent::__construct("Potential race condition detected. $this->message Please try again.", 409);
    }
}