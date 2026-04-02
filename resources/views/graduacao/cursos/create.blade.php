@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h5">Novo curso de graduação</div>
    <div class="card-body">
      <form action="{{ route('graduacao.cursos.store') }}" method="POST">
        @include('graduacao.cursos._form')
      </form>
    </div>
  </div>
@endsection
