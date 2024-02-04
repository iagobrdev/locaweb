<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperacoesRequest;
use App\Services\Operacoes;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(title="API Caixa", version="1.0")
 */
class CaixaController extends Controller
{
    /**
     * @OA\Post(
     *     path="/caixa",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/OperacoesRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Operações bem sucedidas"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro durante as operações"
     *     )
     * )
     */
    public function operacoes(OperacoesRequest $request): JsonResponse
    {
        $output = app(Operacoes::class)->executar($request->all());

        return response()->json($output);
    }
}
