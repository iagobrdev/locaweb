<?php

namespace App\Console\Commands;

use App\Data\Abastecimento;
use App\Data\Saque;
use App\Exceptions\CaixaInexistenteException;
use App\Services\Caixa;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use JetBrains\PhpStorm\NoReturn;

class CaixaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caixa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza operações no caixa';

    /**
     * Execute the console command.
     */
    #[NoReturn] public function handle(): void
    {
        $input = $this->readInput();
        $caixa = app()->make(Caixa::class);

        try {
            foreach ($input as $operacao) {
                if (isset($operacao->caixa)) { // abastecer
                    $caixa->abastecer(new Abastecimento(
                        caixaDisponivel: $operacao->caixa->caixaDisponivel,
                        notas: (array)$operacao->caixa->notas
                    ));
                } else if (isset($operacao->saque)) { // sacar
                    $caixa->sacar(new Saque(
                        valor: $operacao->saque->valor,
                        horario: Carbon::parse($operacao->saque->horario)
                    ));
                } else {
                    throw new Exception('Operação inválida');
                }

                $this->printStatus($caixa);
            }
        } catch (Exception $e) {
            $this->printStatus($caixa, $e);
        }
    }

    public function readInput(): array
    {
        return json_decode(str_replace('\n', '', file_get_contents('php://stdin')));
    }

    public function printStatus(Caixa $caixa, ?Exception $e = null): void
    {
        $status = [
            'caixa' => $e instanceof CaixaInexistenteException ? (object)[] : [
                'caixaDisponivel' => $caixa->disponivel,
                'notas' => $caixa->notas
            ],
            'erros' => $e ? [$e->getMessage()] : []
        ];

        echo (json_encode($status, JSON_PRETTY_PRINT)) . PHP_EOL;
    }
}
