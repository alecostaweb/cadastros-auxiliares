<?php

namespace App\Http\Controllers;

use App\Models\CursoGraduacao;
use Illuminate\Http\JsonResponse;

class ApiGraduacaoCursoController extends Controller
{
    public function index(): JsonResponse
    {
        $cursos = CursoGraduacao::query()
            ->orderBy('codcur')
            ->get()
            ->map(fn (CursoGraduacao $curso): array => $this->toPayload($curso))
            ->values();

        return response()->json($cursos);
    }

    public function show(int $codcur): JsonResponse
    {
        $curso = CursoGraduacao::query()
            ->where('codcur', $codcur)
            ->firstOrFail();

        return response()->json($this->toPayload($curso));
    }

    private function toPayload(CursoGraduacao $curso): array
    {
        return [
            'id' => $curso->id,
            'codcur' => $curso->codcur,
            'nomcur' => $curso->nomcur,
            'codset' => $curso->codset,
            'nomset' => $curso->nomset,
            'nomabvset' => $curso->nomabvset,
        ];
    }
}
