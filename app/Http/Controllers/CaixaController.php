<?php

namespace App\Http\Controllers;

use App\Data\Abastecimento;
use App\Data\Saque;
use App\Exceptions\CaixaInexistenteException;
use App\Http\Requests\AbastecerRequest;
use App\Http\Requests\SacarRequest;
use App\Services\Caixa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(title="API Caixa", version="1.0")
 */
class CaixaController extends Controller
{
    public function __construct(private readonly Caixa $caixa)
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/caixa/abastecer",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AbastecerRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abastecimento bem sucedido"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro durante o abastecimento"
     *     )
     * )
     */
    public function abastecer(AbastecerRequest $request): JsonResponse
    {
        try {
            $this->caixa->abastecer(new Abastecimento(
                caixaDisponivel: $request->caixaDisponivel,
                notas: $request->notas
            ));

            return response()->json($this->formatStatus($this->caixa));
        } catch (Exception $e) {
            return response()->json($this->formatStatus($this->caixa, $e), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/caixa/sacar",
     *     @OA\RequestBody(
     *         description="Dados para saque",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SacarRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Saque bem sucedido"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro durante o saque"
     *     )
     * )
     */
    public function sacar(SacarRequest $request): JsonResponse
    {
        try {
            $this->caixa->sacar(new Saque(
                valor: $request->valor,
                horario: Carbon::parse($request->horario)
            ));

            return response()->json($this->formatStatus($this->caixa));
        } catch (Exception $e) {
            return response()->json($this->formatStatus($this->caixa, $e), 400);
        }
    }

    private function formatStatus(Caixa $caixa, ?Exception $e = null): array
    {
        return [
            'caixa' => $e instanceof CaixaInexistenteException ? [] : [
                'caixaDisponivel' => $caixa->disponivel,
                'notas' => $caixa->notas
            ],
            'erros' => $e ? [$e->getMessage()] : []
        ];
    }
}
