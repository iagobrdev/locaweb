<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="Abastecimento",
 *   type="object",
 *
 *   @OA\Property(
 *     property="caixa",
 *     type="object",
 *     required={"caixaDisponivel","notas"},
 *     @OA\Property(
 *       property="caixaDisponivel",
 *       type="boolean",
 *     ),
 *     @OA\Property(
 *       property="notas",
 *       type="object",
 *       @OA\Property(property="notasCem", type="integer"),
 *       @OA\Property(property="notasCinquenta", type="integer"),
 *       @OA\Property(property="notasVinte", type="integer"),
 *       @OA\Property(property="notasDez", type="integer"),
 *     ),
 *   ),
 * )
 *
 * @OA\Schema(
 *   schema="Saque",
 *   type="object",
 *
 *   @OA\Property(
 *     property="saque",
 *     type="object",
 *     required={"valor","horario"},
 *     @OA\Property(
 *       property="valor",
 *       type="integer",
 *     ),
 *     @OA\Property(
 *       property="horario",
 *       type="string",
 *       format="date-time",
 *     ),
 *   ),
 * )
 *
 * @OA\Schema(
 *   schema="OperacoesRequest",
 *   type="array",
 *
 *   @OA\Items(
 *     type="object",
 *     oneOf={
 *
 *       @OA\Schema(ref="#/components/schemas/Abastecimento"),
 *       @OA\Schema(ref="#/components/schemas/Saque"),
 *     }
 *   )
 * )
 */
class OperacoesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            '*.caixa.caixaDisponivel' => 'required_with:*.caixa|boolean',
            '*.caixa.notas' => 'required_with:*.caixa|array',
            '*.saque.valor' => 'required_with:*.saque|numeric',
            '*.saque.horario' => 'required_with:*.saque|date',
        ];
    }
}
