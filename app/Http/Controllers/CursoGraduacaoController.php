<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCursoGraduacaoRequest;
use App\Http\Requests\UpdateCursoGraduacaoRequest;
use App\Models\CursoGraduacao;
use App\Services\ReplicadoEstruturaService;
use App\Services\ReplicadoGraduacaoService;
use Illuminate\Validation\ValidationException;

class CursoGraduacaoController extends Controller
{
    public function __construct(
        private ReplicadoGraduacaoService $replicadoGraduacao,
        private ReplicadoEstruturaService $replicadoEstrutura,
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index()
    {
        $cursos = CursoGraduacao::query()
            ->orderBy('nomcur')
            ->paginate(20);

        return view('graduacao.cursos.index', compact('cursos'));
    }

    public function create()
    {
        return view('graduacao.cursos.create');
    }

    public function store(StoreCursoGraduacaoRequest $request)
    {
        $dados = $request->validated();
        $dadosComplementares = $this->montarDadosComplementares((int) $dados['codcur'], $dados['codset'] ?? null);

        CursoGraduacao::query()->create(array_merge($dados, $dadosComplementares));

        return redirect()
            ->route('graduacao.cursos.index')
            ->with('success', 'Curso de graduação cadastrado com sucesso.');
    }

    public function show(CursoGraduacao $curso)
    {
        return view('graduacao.cursos.show', compact('curso'));
    }

    public function edit(CursoGraduacao $curso)
    {
        return view('graduacao.cursos.edit', compact('curso'));
    }

    public function update(UpdateCursoGraduacaoRequest $request, CursoGraduacao $curso)
    {
        $dados = $request->validated();
        $dadosComplementares = $this->montarDadosComplementares($curso->codcur, $dados['codset'] ?? null);

        $curso->update(array_merge($dados, $dadosComplementares));

        return redirect()
            ->route('graduacao.cursos.index')
            ->with('success', 'Curso de graduação atualizado com sucesso.');
    }

    public function destroy(CursoGraduacao $curso)
    {
        $curso->delete();

        return redirect()
            ->route('graduacao.cursos.index')
            ->with('success', 'Curso de graduação removido com sucesso.');
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

    private function montarDadosComplementares(int $codcur, mixed $codset): array
    {
        $cursoReplicado = $this->replicadoGraduacao->obterCurso($codcur);

        if (!is_array($cursoReplicado)) {
            throw ValidationException::withMessages([
                'codcur' => 'Curso não encontrado no replicado.',
            ]);
        }

        $nomeCurso = $this->valorCurso($cursoReplicado, 'nomcur');

        if (!is_string($nomeCurso) || trim($nomeCurso) === '') {
            throw ValidationException::withMessages([
                'codcur' => 'Nome do curso não encontrado no replicado.',
            ]);
        }

        if ($codset === null || $codset === '') {
            return [
                'nomcur' => $nomeCurso,
                'nomset' => null,
                'nomabvset' => null,
            ];
        }

        $codsetInt = (int) $codset;
        $setor = $this->replicadoEstrutura->obterSetor($codsetInt);

        if (!is_array($setor)) {
            throw ValidationException::withMessages([
                'codset' => 'Setor não encontrado no replicado.',
            ]);
        }

        return [
            'nomcur' => $nomeCurso,
            'nomset' => $this->textoOuNull($this->valorSetor($setor, 'nomset')),
            'nomabvset' => $this->textoOuNull($this->valorSetor($setor, 'nomabvset')),
        ];
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

    private function textoOuNull(mixed $valor): ?string
    {
        return is_string($valor) ? $valor : null;
    }
}
