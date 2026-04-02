<?php

namespace Tests\Feature;

use App\Models\CursoGraduacao;
use App\Models\User;
use App\Services\ReplicadoEstruturaService;
use App\Services\ReplicadoGraduacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CursoGraduacaoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_redireciona_visitante_para_login(): void
    {
        $response = $this->get('/graduacao/cursos');

        $response->assertRedirect('/login');
    }

    public function test_admin_lista_cursos_de_graduacao(): void
    {
        $admin = $this->criarAdmin();

        CursoGraduacao::query()->create([
            'codcur' => 5101,
            'nomcur' => 'Curso Alfa',
            'codset' => 558,
            'nomset' => 'Departamento Alfa',
            'nomabvset' => 'DA',
        ]);

        $response = $this->actingAs($admin)->get('/graduacao/cursos');

        $response->assertOk();
        $response->assertSee('Cursos de Graduação');
        $response->assertSee('5101');
        $response->assertSee('Curso Alfa');
        $response->assertSee('558');
    }

    public function test_admin_cria_curso_de_graduacao(): void
    {
        $admin = $this->criarAdmin();

        $this->mock(ReplicadoGraduacaoService::class, function ($mock) {
            $mock->shouldReceive('obterCurso')
                ->once()
                ->with(6201)
                ->andReturn([
                    'codcur' => 6201,
                    'nomcur' => 'Curso Beta',
                ]);
        });

        $this->mock(ReplicadoEstruturaService::class, function ($mock) {
            $mock->shouldReceive('obterSetor')
                ->once()
                ->with(559)
                ->andReturn([
                    'codset' => 559,
                    'nomset' => 'Departamento Beta',
                    'nomabvset' => 'DB',
                ]);
        });

        $response = $this->actingAs($admin)->post('/graduacao/cursos', [
            'codcur' => 6201,
            'codset' => 559,
        ]);

        $response->assertRedirect('/graduacao/cursos');

        $this->assertDatabaseHas('cursos_graduacao', [
            'codcur' => 6201,
            'nomcur' => 'Curso Beta',
            'codset' => 559,
            'nomset' => 'Departamento Beta',
            'nomabvset' => 'DB',
        ]);
    }

    public function test_admin_atualiza_curso_de_graduacao(): void
    {
        $admin = $this->criarAdmin();

        $curso = CursoGraduacao::query()->create([
            'codcur' => 7301,
            'nomcur' => 'Curso Antigo',
            'codset' => 558,
            'nomset' => 'Departamento Antigo',
            'nomabvset' => 'DA',
        ]);

        $this->mock(ReplicadoGraduacaoService::class, function ($mock) {
            $mock->shouldReceive('obterCurso')
                ->once()
                ->with(7301)
                ->andReturn([
                    'codcur' => 7301,
                    'nomcur' => 'Curso Gama',
                ]);
        });

        $this->mock(ReplicadoEstruturaService::class, function ($mock) {
            $mock->shouldReceive('obterSetor')
                ->once()
                ->with(560)
                ->andReturn([
                    'codset' => 560,
                    'nomset' => 'Departamento Gama',
                    'nomabvset' => 'DG',
                ]);
        });

        $response = $this->actingAs($admin)->put("/graduacao/cursos/{$curso->id}", [
            'codset' => 560,
        ]);

        $response->assertRedirect('/graduacao/cursos');

        $this->assertDatabaseHas('cursos_graduacao', [
            'id' => $curso->id,
            'codcur' => 7301,
            'nomcur' => 'Curso Gama',
            'codset' => 560,
            'nomset' => 'Departamento Gama',
            'nomabvset' => 'DG',
        ]);
    }

    public function test_admin_visualiza_show_com_nome_do_curso(): void
    {
        $admin = $this->criarAdmin();

        $curso = CursoGraduacao::query()->create([
            'codcur' => 7401,
            'nomcur' => 'Curso Ômega',
            'codset' => 558,
            'nomset' => 'Departamento Ômega',
            'nomabvset' => 'DO',
        ]);

        $response = $this->actingAs($admin)->get("/graduacao/cursos/{$curso->id}");

        $response->assertOk();
        $response->assertSee('Curso Ômega');
    }

    public function test_admin_visualiza_edit_com_nome_do_curso(): void
    {
        $admin = $this->criarAdmin();

        $curso = CursoGraduacao::query()->create([
            'codcur' => 7501,
            'nomcur' => 'Curso Sigma',
            'codset' => 558,
            'nomset' => 'Departamento Sigma',
            'nomabvset' => 'DS',
        ]);

        $this->mock(ReplicadoEstruturaService::class, function ($mock) {
            $mock->shouldReceive('listarDepartamentosDeEnsino')
                ->once()
                ->andReturn([
                    [
                        'codset' => 558,
                        'nomset' => 'Departamento Sigma',
                        'nomabvset' => 'DS',
                    ],
                ]);
        });

        $response = $this->actingAs($admin)->get("/graduacao/cursos/{$curso->id}/edit");

        $response->assertOk();
        $response->assertSee('Curso Sigma');
    }

    public function test_admin_nao_consegue_editar_codcur_no_update(): void
    {
        $admin = $this->criarAdmin();

        $curso = CursoGraduacao::query()->create([
            'codcur' => 7601,
            'nomcur' => 'Curso Iota',
            'codset' => 558,
            'nomset' => 'Departamento Iota',
            'nomabvset' => 'DI',
        ]);

        $response = $this->actingAs($admin)->put("/graduacao/cursos/{$curso->id}", [
            'codcur' => 9999,
            'codset' => 561,
        ]);

        $response->assertSessionHasErrors('codcur');

        $this->assertDatabaseHas('cursos_graduacao', [
            'id' => $curso->id,
            'codcur' => 7601,
            'nomcur' => 'Curso Iota',
            'codset' => 558,
            'nomset' => 'Departamento Iota',
            'nomabvset' => 'DI',
        ]);
    }

    public function test_admin_remove_curso_de_graduacao(): void
    {
        $admin = $this->criarAdmin();

        $curso = CursoGraduacao::query()->create([
            'codcur' => 8701,
            'nomcur' => 'Curso Epsilon',
            'codset' => 558,
            'nomset' => 'Departamento Epsilon',
            'nomabvset' => 'DE',
        ]);

        $response = $this->actingAs($admin)->delete("/graduacao/cursos/{$curso->id}");

        $response->assertRedirect('/graduacao/cursos');
        $this->assertDatabaseMissing('cursos_graduacao', ['id' => $curso->id]);
    }

    private function criarAdmin(): User
    {
        Permission::findOrCreate('admin', 'web');

        $user = User::factory()->create();
        $user->givePermissionTo('admin');

        return $user;
    }
}
