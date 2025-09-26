<?php

namespace App\Exceptions;

use Exception;

class WrongPasswordException extends Exception
{
    public function __construct($message = 'Email atau password salah.')
    {
        parent::__construct($message);
    }
}
