<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     description="Dados para saque",
 *     type="object",
 *     required={"valor", "horario"},
 *
 *     @OA\Property(
 *         property="valor",
 *         type="number",
 *     ),
 *
 *     @OA\Property(
 *         property="horario",
 *         type="string",
 *         format="date-time",
 *     )
 * )
 */
class SacarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'valor' => 'required|numeric',
            'horario' => 'required|date',
        ];
    }
}
