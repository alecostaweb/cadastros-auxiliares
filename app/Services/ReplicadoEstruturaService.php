<?php

namespace App\Services;

use Uspdev\Replicado\Estrutura;

class ReplicadoEstruturaService
{
    public function listarDepartamentosDeEnsino(?int $codund = null): array
    {
        $setores = Estrutura::listarSetores($codund);

        return collect($setores)
            ->filter(fn (mixed $setor): bool => is_array($setor))
            ->filter(function (array $setor): bool {
                $tipset = $this->valorSetor($setor, 'tipset');

                return is_string($tipset) && trim($tipset) === 'Departamento de Ensino';
            })
            ->map(function (array $setor): array {
                return [
                    'codset' => $this->valorSetor($setor, 'codset'),
                    'nomset' => $this->valorSetor($setor, 'nomset'),
                    'nomabvset' => $this->valorSetor($setor, 'nomabvset'),
                ];
            })
            ->sortBy('nomset')
            ->values()
            ->all();
    }

    public function obterSetor(int $codset): ?array
    {
        $setor = Estrutura::dump($codset);

        return is_array($setor) ? $setor : null;
    }

    private function valorSetor(array $setor, string $campo): mixed
    {
        if (array_key_exists($campo, $setor)) {
            return $setor[$campo];
        }

        $campoUpper = strtoupper($campo);
        if (array_key_exists($campoUpper, $setor)) {
            return $setor[$campoUpper];
        }

        return null;
    }
}
