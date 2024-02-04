<?php

namespace App\Exceptions;

use Exception;

class OperacaoInvalidaException extends Exception
{
    public function __construct()
    {
        parent::__construct('operacao-invalida');
    }
}
