<?php

namespace App\Data;

class Abastecimento
{
    public function __construct(public bool $caixaDisponivel, public array $notas)
    {
        //
    }
}
