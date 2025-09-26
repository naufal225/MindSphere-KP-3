<?php

namespace App\Exceptions;

use Exception;

class NotAdminException extends Exception
{
    public function __construct($message = 'Akses hanya untuk admin.')
    {
        parent::__construct($message);
    }
}
