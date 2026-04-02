<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCursoGraduacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codcur' => ['required', 'integer', 'min:1', 'unique:cursos_graduacao,codcur'],
            'codset' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
