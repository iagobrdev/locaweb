<?php

namespace App\Exceptions;

use Exception;

class ValorIndisponivelException extends Exception
{
    public function __construct()
    {
        parent::__construct('valor-indisponivel');
    }
}
