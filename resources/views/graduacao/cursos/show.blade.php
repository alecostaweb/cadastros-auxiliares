@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h5">Curso de graduação #{{ $curso->id }}</div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Código</dt>
        <dd class="col-sm-9">{{ $curso->codcur }}</dd>

        <dt class="col-sm-3">Curso</dt>
        <dd class="col-sm-9">{{ $curso->nomcur ?? '-' }}</dd>

        <dt class="col-sm-3">Código do setor</dt>
        <dd class="col-sm-9">{{ $curso->codset ?? '-' }}</dd>

        <dt class="col-sm-3">Setor</dt>
        <dd class="col-sm-9">{{ $curso->nomset ?? '-' }}</dd>

        <dt class="col-sm-3">Sigla do setor</dt>
        <dd class="col-sm-9">{{ $curso->nomabvset ?? '-' }}</dd>
      </dl>

      <div class="mt-3">
        <a href="{{ route('graduacao.cursos.edit', $curso) }}" class="btn btn-primary">Editar</a>
        <a href="{{ route('graduacao.cursos.index') }}" class="btn btn-secondary">Voltar</a>
      </div>
    </div>
  </div>
@endsection
