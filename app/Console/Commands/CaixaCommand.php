<?php

namespace App\Console\Commands;

use App\Services\Operacoes;
use Illuminate\Console\Command;

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
    public function handle(): void
    {
        $output = app(Operacoes::class)->executar($this->readInput());

        $this->line(json_encode($output, JSON_PRETTY_PRINT));
    }

    private function readInput(): array
    {
        $content = str_replace(['\n', '\r'], '', file_get_contents('php://stdin'));

        return json_decode($content, true);
    }
}
