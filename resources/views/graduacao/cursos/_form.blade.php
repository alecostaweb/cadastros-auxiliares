@csrf

<div class="mb-3">
  <label for="codcur" class="form-label">Código</label>
  @if(isset($curso))
    <input
      type="text"
      id="codcur"
      class="form-control"
      value="{{ $curso->codcur }}"
      disabled
    >
  @else
    <input
      type="number"
      id="codcur"
      name="codcur"
      class="form-control @error('codcur') is-invalid @enderror"
      value="{{ old('codcur', $curso->codcur ?? '') }}"
      required
      min="1"
    >
  @endif
  @error('codcur')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
  <div class="form-text">Código do curso no replicado.</div>
</div>

<div class="mb-3">
  <label for="codset" class="form-label">Código do setor</label>
  @php
    $codsetSelecionado = old('codset', $curso->codset ?? '');
  @endphp
  <select
    id="codset"
    name="codset"
    class="form-control select2 @error('codset') is-invalid @enderror"
  >
    <option value="">Selecione um departamento</option>
    @foreach(($setores ?? []) as $setor)
      <option value="{{ $setor['codset'] }}" @selected((string) $codsetSelecionado === (string) $setor['codset'])>
        {{ $setor['codset'] }} - {{ $setor['nomset'] ?? '-' }} ({{ $setor['nomabvset'] ?? '-' }})
      </option>
    @endforeach
  </select>
  @error('codset')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
  <div class="form-text">Selecione um setor com tipo Departamento de Ensino.</div>
</div>

@if(isset($curso))
  <div class="mb-3">
    <label class="form-label">Curso</label>
    <input
      type="text"
      class="form-control"
      value="{{ $curso->nomcur ?? '-' }}"
      disabled
    >
  </div>

  <div class="mb-3">
    <label class="form-label">Setor</label>
    <input
      type="text"
      class="form-control"
      value="{{ $curso->nomset ?? '-' }}"
      disabled
    >
  </div>

  <div class="mb-3">
    <label class="form-label">Sigla</label>
    <input
      type="text"
      class="form-control"
      value="{{ $curso->nomabvset ?? '-' }}"
      disabled
    >
  </div>
@else
  <div class="alert alert-light border small mb-3">
    <strong>Preenchimento automático:</strong> ao salvar, o sistema persiste <code>nomcur</code>, <code>nomset</code> e <code>nomabvset</code> com base em <code>codcur</code> e <code>codset</code>.
  </div>
@endif

<button type="submit" class="btn btn-primary">Salvar</button>
<a href="{{ route('graduacao.cursos.index') }}" class="btn btn-secondary">Cancelar</a>
