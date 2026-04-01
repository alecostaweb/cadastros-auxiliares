@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="h5 mb-0">Cursos de Graduação</span>
      <a href="{{ route('graduacao.cursos.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Novo curso
      </a>
    </div>
    <div class="card-body">
      @if($cursos->isEmpty())
        <p class="mb-0">Nenhum curso cadastrado.</p>
      @else
        <div class="table-responsive">
          <table class="table table-striped datatable-simples">
            <thead>
              <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Curso</th>
                <th>Cód. setor</th>
                <th>Setor</th>
                <th>Sigla setor</th>
                <th>Atualizado</th>
                <th class="text-end">Ações</th>
              </tr>
            </thead>
            <tbody>
              @foreach($cursos as $curso)
                <tr>
                  <td>{{ $curso->id }}</td>
                  <td>{{ $curso->codcur }}</td>
                  <td>{{ $curso->nomcur ?? '-' }}</td>
                  <td>{{ $curso->codset ?? '-' }}</td>
                  <td>{{ $curso->nomset ?? '-' }}</td>
                  <td>{{ $curso->nomabvset ?? '-' }}</td>
                  <td>{{ $curso->updated_at?->format('d/m/Y H:i') }}</td>
                  <td class="text-end">
                    <a href="{{ route('graduacao.cursos.show', $curso) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    <a href="{{ route('graduacao.cursos.edit', $curso) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                    <form action="{{ route('graduacao.cursos.destroy', $curso) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Confirma a exclusão?')">
                        Excluir
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{ $cursos->links() }}
      @endif
    </div>
  </div>
@endsection
