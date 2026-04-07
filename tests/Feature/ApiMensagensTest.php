<?php

namespace Tests\Feature;

use App\Models\Mensagem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiMensagensTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cadastros-auxiliares.password', '');
    }

    public function test_lista_mensagens_ativas_filtradas_e_ordenadas(): void
    {
        Mensagem::factory()->create([
            'titulo' => 'Mensagem ativa empresta docente',
            'sistema' => 'empresta',
            'publico' => true,
            'ativo' => true,
            'prioridade' => 20,
            'inicio_exibicao' => now()->subDay(),
            'fim_exibicao' => now()->addDay(),
        ]);

        Mensagem::factory()->create([
            'titulo' => 'Mensagem geral ativa',
            'sistema' => 'geral',
            'publico' => true,
            'ativo' => true,
            'prioridade' => 10,
            'inicio_exibicao' => now()->subDay(),
            'fim_exibicao' => now()->addDay(),
        ]);

        Mensagem::factory()->create([
            'titulo' => 'Mensagem inativa',
            'sistema' => 'empresta',
            'publico' => true,
            'ativo' => false,
            'prioridade' => 99,
        ]);

        $response = $this->getJson('/api/mensagens?sistema=empresta&publico=Docente&ativos=true');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonPath('0.titulo', 'Mensagem ativa empresta docente');
        $response->assertJsonPath('1.titulo', 'Mensagem geral ativa');
    }

    public function test_respeita_limite(): void
    {
        Mensagem::factory()->count(3)->create([
            'ativo' => true,
            'inicio_exibicao' => now()->subDay(),
            'fim_exibicao' => now()->addDay(),
        ]);

        $response = $this->getJson('/api/mensagens?ativos=true&limite=2');

        $response->assertOk();
        $response->assertJsonCount(2);
    }

    public function test_bloqueia_sem_password_quando_configurada(): void
    {
        config()->set('cadastros-auxiliares.password', 'segredo-123');

        $response = $this->getJson('/api/mensagens?ativos=true');

        $response->assertUnauthorized();
    }

    public function test_permite_com_password_na_query(): void
    {
        config()->set('cadastros-auxiliares.password', 'segredo-123');
        Mensagem::factory()->create(['ativo' => true, 'publico' => true]);

        $response = $this->getJson('/api/mensagens?ativos=true&password=segredo-123');

        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_permite_usuario_web_autenticado_sem_password(): void
    {
        config()->set('cadastros-auxiliares.password', 'segredo-123');
        Mensagem::factory()->create(['ativo' => true, 'publico' => true]);
        $this->actingAs(User::factory()->create());

        $response = $this->get('/api/mensagens?ativos=true', [
            'Accept' => 'application/json',
        ]);

        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_permite_requisicao_interna_do_tema_sem_password(): void
    {
        config()->set('cadastros-auxiliares.password', 'segredo-123');
        Mensagem::factory()->create(['ativo' => true, 'publico' => true]);

        $response = $this->withHeaders([
            'X-UspTheme-Mensagens-Internal' => '1',
        ])->getJson('/api/mensagens?ativos=true');

        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_permite_requisicao_same_origin_sem_password(): void
    {
        config()->set('cadastros-auxiliares.password', 'segredo-123');
        Mensagem::factory()->create(['ativo' => true, 'publico' => true]);

        $response = $this->withHeaders([
            'Referer' => config('app.url') . '/alguma-pagina',
        ])->getJson('/api/mensagens?ativos=true');

        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_sanitiza_conteudo_html_permitindo_somente_tags_da_whitelist(): void
    {
        Mensagem::factory()->create([
            'ativo' => true,
            'publico' => true,
            'conteudo' => '<p><strong>Aviso</strong> <em>importante</em><br>'
                . '<a href="javascript:alert(1)" onclick="alert(2)">link inseguro</a> '
                . '<a href="https://example.com" style="color:red">link seguro</a>'
                . '<script>alert(3)</script><span>texto span</span></p>',
        ]);

        $response = $this->getJson('/api/mensagens?ativos=true');

        $response->assertOk();

        $conteudo = $response->json('0.conteudo');

        $this->assertIsString($conteudo);
        $this->assertStringContainsString('<p>', $conteudo);
        $this->assertStringContainsString('<strong>Aviso</strong>', $conteudo);
        $this->assertStringContainsString('<em>importante</em>', $conteudo);
        $this->assertStringContainsString('<br>', $conteudo);
        $this->assertStringContainsString('<a>link inseguro</a>', $conteudo);
        $this->assertStringContainsString(
            '<a href="https://example.com" target="_blank" rel="noopener noreferrer">link seguro</a>',
            $conteudo
        );
        $this->assertStringContainsString('texto span', $conteudo);
        $this->assertStringNotContainsString('<script>', $conteudo);
        $this->assertStringNotContainsString('javascript:', $conteudo);
        $this->assertStringNotContainsString('onclick=', $conteudo);
    }
}
