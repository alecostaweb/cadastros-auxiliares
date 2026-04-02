<?php

namespace App\Http\Requests;

class UpdateCursoGraduacaoRequest extends StoreCursoGraduacaoRequest
{
    public function rules(): array
    {
        return [
            'codcur' => ['prohibited'],
            'codset' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
