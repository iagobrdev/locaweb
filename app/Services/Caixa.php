<?php

namespace App\Services;

use App\Data\Abastecimento;
use App\Data\Saque;
use App\Exceptions\CaixaEmUsoException;
use App\Exceptions\CaixaInexistenteException;
use App\Exceptions\SaqueDuplicadoException;
use App\Exceptions\ValorIndisponivelException;
use stdClass;

class Caixa
{
    /**
     * Notas aceitas pelo caixa para abastecimentos e saques,
     * sendo a chave o nome da nota e o valor o seu respectivo valor.
     */
    public const NOTAS_ACEITAS = [
        'notasCem' => 100,
        'notasCinquenta' => 50,
        'notasVinte' => 20,
        'notasDez' => 10,
    ];

    /**
     * Verifica se o caixa está disponível, ou seja, já pode ser utilizado para saques.
     */
    public bool $disponivel = false;

    /**
     * Notas disponíveis no caixa, com a quantidade de cada uma.
     *
     * @var array<string, int>
     */
    public array $notas = [
        'notasCem' => 0,
        'notasCinquenta' => 0,
        'notasVinte' => 0,
        'notasDez' => 0,
    ];

    /**
     * Total de abastecimentos realizados no caixa.
     *
     * @var array<Abastecimento>
     */
    public array $abastecimentos = [];

    /**
     * Total de saques realizados no caixa.
     *
     * @var array<Saque>
     */
    public array $saques = [];

    /**
     * Realiza um abastecimento no caixa, adicionando notas ao mesmo.
     * Caso o caixa já esteja disponível para saques, lança uma exceção.
     *
     * @throws CaixaEmUsoException
     */
    public function abastecer(Abastecimento $abastecimento): void
    {
        if ($this->disponivel) {
            throw new CaixaEmUsoException();
        }

        $this->disponivel = $abastecimento->caixaDisponivel;

        foreach ($abastecimento->notas as $nota => $quantidade) {
            if (!array_key_exists($nota, self::NOTAS_ACEITAS)) {
                continue;
            }

            $this->notas[$nota] += $quantidade;
        }

        $this->abastecimentos[] = $abastecimento;
    }

    /**
     * Realiza um saque no caixa, retirando notas do mesmo.
     * Caso o caixa ainda não estejá disponível, lança uma exceção.
     * Caso haja dois saques de mesmo valor em menos de 10 minutos, lança uma exceção.
     * Caso não haja saldo ou notas suficientes, lança uma exceção.
     *
     * @throws CaixaInexistenteException
     * @throws SaqueDuplicadoException
     * @throws ValorIndisponivelException
     */
    public function sacar(Saque $saque): array
    {
        if (!$this->disponivel) {
            throw new CaixaInexistenteException();
        }

        $valor = $saque->valor;

        if ($this->saldo() < $valor) {
            throw new ValorIndisponivelException();
        }

        // Verifique se houve um saque bem-sucedido com o mesmo valor nos últimos 10 minutos
        foreach ($this->saques as $saqueAnterior) {
            if ($saque->verificaDuplicidade($saqueAnterior, 10)) {
                throw new SaqueDuplicadoException();
            }
        }

        // Inicialize $notasSaque como array equivalente a NOTAS_ACEITAS com valores 0
        $notasSaque = array_combine(array_keys(self::NOTAS_ACEITAS), array_fill(0, count(self::NOTAS_ACEITAS), 0));

        // Ordenar as notas em ordem decrescente para fazer o saque começar da maior nota
        $notasAceitas = self::NOTAS_ACEITAS;
        arsort($notasAceitas);

        foreach ($notasAceitas as $nota => $valorNota) {
            while ($this->notas[$nota] > 0 && $valorNota <= $valor) {
                $valor -= $valorNota; // reduz o valor a sacar
                $this->notas[$nota]--; // diminui a quantidade de notas no caixa
                $notasSaque[$nota]++; // acrescenta a quantidade de notas sacadas
            }
        }

        // Se o valor final é diferente de zero, significa que não foi possível fornecer o saque nas notas disponíveis.
        if ($valor > 0) {
            throw new ValorIndisponivelException();
        }

        $this->saques[] = $saque;

        return $notasSaque;
    }

    /**
     * Calcula o saldo total do caixa, com base nas notas disponíveis.
     */
    public function saldo(): int
    {
        $saldo = 0;

        foreach ($this->notas as $nota => $quantidade) {
            $saldo += self::NOTAS_ACEITAS[$nota] * $quantidade;
        }

        return $saldo;
    }
}
