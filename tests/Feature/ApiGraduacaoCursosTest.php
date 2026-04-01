<?php

namespace Tests\Feature;

use App\Models\CursoGraduacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiGraduacaoCursosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cadastros-auxiliares.password', '');
    }

    public function test_lista_cursos_de_graduacao(): void
    {
        CursoGraduacao::query()->create([
            'codcur' => 1002,
            'nomcur' => 'Química',
            'codset' => 559,
            'nomset' => 'Departamento de Química',
            'nomabvset' => 'DQ',
        ]);

        CursoGraduacao::query()->create([
            'codcur' => 1001,
            'nomcur' => 'Matemática',
            'codset' => 558,
            'nomset' => 'Departamento de Matemática',
            'nomabvset' => 'DM',
        ]);

        $response = $this->getJson('/api/graduacao/cursos');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonPath('0.codcur', 1001);
        $response->assertJsonPath('0.nomcur', 'Matemática');
        $response->assertJsonPath('0.codset', 558);
        $response->assertJsonPath('0.nomset', 'Departamento de Matemática');
        $response->assertJsonPath('0.nomabvset', 'DM');
        $response->assertJsonPath('1.codcur', 1002);
        $response->assertJsonPath('1.nomcur', 'Química');
        $response->assertJsonPath('1.codset', 559);
        $response->assertJsonPath('1.nomset', 'Departamento de Química');
        $response->assertJsonPath('1.nomabvset', 'DQ');
    }

    public function test_obtem_curso_de_graduacao_por_codcur(): void
    {
        $curso = CursoGraduacao::query()->create([
            'codcur' => 1234,
            'nomcur' => 'Relações Públicas',
            'codset' => 558,
            'nomset' => 'Departamento de Relações Públicas, Propaganda e Turismo',
            'nomabvset' => 'CRP',
        ]);

        $response = $this->getJson('/api/graduacao/cursos/1234');

        $response->assertOk();
        $response->assertJsonPath('id', $curso->id);
        $response->assertJsonPath('codcur', 1234);
        $response->assertJsonPath('nomcur', 'Relações Públicas');
        $response->assertJsonPath('codset', 558);
        $response->assertJsonPath('nomset', 'Departamento de Relações Públicas, Propaganda e Turismo');
        $response->assertJsonPath('nomabvset', 'CRP');
    }

    public function test_retorna_404_quando_curso_nao_esta_cadastrado_localmente(): void
    {
        $response = $this->getJson('/api/graduacao/cursos/9999');

        $response->assertNotFound();
    }

    public function test_retorna_dados_nulos_de_setor_quando_nao_ha_setor_cadastrado(): void
    {
        $curso = CursoGraduacao::query()->create([
            'codcur' => 4321,
            'nomcur' => 'Geografia',
            'codset' => null,
            'nomset' => null,
            'nomabvset' => null,
        ]);

        $response = $this->getJson('/api/graduacao/cursos/4321');

        $response->assertOk();
        $response->assertJsonPath('id', $curso->id);
        $response->assertJsonPath('codcur', 4321);
        $response->assertJsonPath('nomcur', 'Geografia');
        $response->assertJsonPath('codset', null);
        $response->assertJsonPath('nomset', null);
        $response->assertJsonPath('nomabvset', null);
    }
}
