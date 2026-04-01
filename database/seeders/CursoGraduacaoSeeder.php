<?php

namespace Database\Seeders;

use App\Models\CursoGraduacao;
use Illuminate\Database\Seeder;
use Uspdev\Replicado\Graduacao;

class CursoGraduacaoSeeder extends Seeder
{
    public function run(): void
    {
        $cursosReplicado = Graduacao::listarCursos();

        foreach ($cursosReplicado as $curso) {
            if (!is_array($curso)) {
                continue;
            }

            $codcur = $this->valor($curso, 'codcur');

            if (!is_numeric($codcur)) {
                continue;
            }

            $nomcur = $this->valor($curso, 'nomcur');

            CursoGraduacao::query()->firstOrCreate(
                ['codcur' => (int) $codcur],
                [
                    'nomcur' => is_string($nomcur) ? $nomcur : null,
                    'codset' => null,
                    'nomset' => null,
                    'nomabvset' => null,
                ]
            );
        }
    }

    private function valor(array $dados, string $campo): mixed
    {
        if (array_key_exists($campo, $dados)) {
            return $dados[$campo];
        }

        $campoUpper = strtoupper($campo);
        if (array_key_exists($campoUpper, $dados)) {
            return $dados[$campoUpper];
        }

        return null;
    }
}
