<?php

namespace App\Services;

use App\Data\Abastecimento;
use App\Data\Saque;
use App\Exceptions\CaixaInexistenteException;
use App\Exceptions\OperacaoInvalidaException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class Operacoes
{
    public function __construct(private readonly Caixa $caixa)
    {
        //
    }

    /**
     * Executa múltiplas operações no caixa.
     * Retorna uma lista com os resultados de cada operação.
     */
    public function executar(array $operacoes): Collection
    {
        $resultados = collect();

        foreach ($operacoes as $operacao) {
            try {
                match (array_key_first($operacao)) {
                    'caixa' => $this->caixa->abastecer(new Abastecimento(
                        caixaDisponivel: $operacao['caixa']['caixaDisponivel'],
                        notas: $operacao['caixa']['notas']
                    )),

                    'saque' => $this->caixa->sacar(new Saque(
                        valor: $operacao['saque']['valor'],
                        horario: Carbon::parse($operacao['saque']['horario'])
                    )),

                    default => throw new OperacaoInvalidaException(),
                };

                $resultados->push($this->status());
            } catch (Exception $e) {
                $resultados->push($this->status($e));
            }
        }

        return $resultados;
    }

    private function status(?Exception $exception = null): array
    {
        $status = [
            'caixa' => ! $exception instanceof CaixaInexistenteException ? [
                'caixaDisponivel' => $this->caixa->disponivel,
                'notas' => $this->caixa->notas,
            ] : [],
            'erros' => $exception ? [$exception->getMessage()] : [],
        ];

        return unserialize(serialize($status));
    }
}
