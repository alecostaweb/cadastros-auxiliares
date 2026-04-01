<?php

namespace App\Services;

use Uspdev\Replicado\Graduacao;

class ReplicadoGraduacaoService
{
    public function listarCursos(): array
    {
        return Graduacao::listarCursos();
    }

    public function obterCurso(int $codcur): ?array
    {
        foreach ($this->listarCursos() as $curso) {
            if (!is_array($curso)) {
                continue;
            }

            $valorCodcur = $this->valorCurso($curso, 'codcur');

            if (is_numeric($valorCodcur) && (int) $valorCodcur === $codcur) {
                return $curso;
            }
        }

        return null;
    }

    private function valorCurso(array $curso, string $campo): mixed
    {
        if (array_key_exists($campo, $curso)) {
            return $curso[$campo];
        }

        $campoUpper = strtoupper($campo);
        if (array_key_exists($campoUpper, $curso)) {
            return $curso[$campoUpper];
        }

        return null;
    }
}
