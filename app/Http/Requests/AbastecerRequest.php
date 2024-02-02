<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     description="Dados para abastecimento",
 *     type="object",
 *     required={"caixaDisponivel", "notas"},
 *
 *     @OA\Property(
 *         property="caixaDisponivel",
 *         type="boolean",
 *     ),
 *
 *     @OA\Property(
 *         property="notas",
 *         type="object",
 *         example={"notasDez" : 10, "notasVinte" : 10, "notasCinquenta" : 10, "notasCem" : 10},
 *     )
 * )
 */
class AbastecerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'caixaDisponivel' => 'required|boolean',
            'notas' => 'required|array',
        ];
    }
}
