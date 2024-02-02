<?php

namespace App\Exceptions;

use Exception;

class SaqueDuplicadoException extends Exception
{
    public function __construct()
    {
        parent::__construct('saque-duplicado');
    }
}
