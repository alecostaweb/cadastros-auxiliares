<?php

namespace App\Services;

use Uspdev\Replicado\Estrutura;

class ReplicadoEstruturaService
{
    public function obterSetor(int $codset): ?array
    {
        $setor = Estrutura::dump($codset);

        return is_array($setor) ? $setor : null;
    }
}
