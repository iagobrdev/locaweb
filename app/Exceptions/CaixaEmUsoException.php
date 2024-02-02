<?php

namespace App\Exceptions;

use Exception;

class CaixaEmUsoException extends Exception
{
    public function __construct()
    {
        parent::__construct('caixa-em-uso');
    }
}
