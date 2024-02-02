<?php

namespace App\Exceptions;

use Exception;

class CaixaInexistenteException extends Exception
{
    public function __construct()
    {
        parent::__construct('caixa-inexistente');
    }
}
