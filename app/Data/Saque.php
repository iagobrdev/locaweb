<?php

namespace App\Data;

use Carbon\Carbon;

class Saque
{
    public function __construct(public int $valor, public Carbon $horario)
    {
    }

    /**
     * Verifica se o saque atual é duplicado em relação ao saque anterior.
     *
     * @param  Saque  $saqueAnterior  Saque anterior
     * @param  int  $minutos  Tempo mínimo entre saques
     * @return bool Retorna verdadeiro se o saque atual for duplicado em relação ao saque anterior
     */
    public function verificaDuplicidade(Saque $saqueAnterior, int $minutos): bool
    {
        return $this->valor === $saqueAnterior->valor && $saqueAnterior->horario->gt(now()->subMinutes($minutos));
    }
}
