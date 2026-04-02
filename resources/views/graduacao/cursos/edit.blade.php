@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h5">Editar curso de graduação #{{ $curso->id }}</div>
    <div class="card-body">
      <form action="{{ route('graduacao.cursos.update', $curso) }}" method="POST">
        @method('PUT')
        @include('graduacao.cursos._form')
      </form>
    </div>
  </div>
@endsection
