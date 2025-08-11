@php
  $get = fn($key, $default = '') => data_get($estudiante ?? [], $key, $default);
@endphp

@extends('layouts.ucmc_form_actualizacion')

@section('title','Actualización de datos | Universidad Colegio Mayor de Cundinamarca')

@push('styles')
<style>
  h1{margin:0 0 8px 0;font-size:22px}
  p.lead{margin:0 0 16px 0;color:#374151}
  .grid{display:grid;gap:12px}
  @media (min-width:640px){ .grid-2{grid-template-columns:repeat(2,1fr)} }
  @media (min-width:940px){ .grid-3{grid-template-columns:repeat(3,1fr)} }
  label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}
  input,select,textarea{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:#fff;color:#111827}
  input[readonly]{background:#f9fafb;color:#4b5563}
  .help{font-size:12px;color:#6b7280;margin-top:4px}
  .actions{display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end;margin-top:16px}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;border:1px solid transparent;font-weight:600;cursor:pointer;text-decoration:none;font-size:14px}
  .btn-primary{background:var(--ucmc-verde);color:#fff}
  .btn-secondary{background:#e5e7eb;color:#111827}
  .btn:hover{filter:brightness(0.96)}
  .divider{height:1px;background:#e5e7eb;margin:16px 0}
</style>
@endpush

@section('content')
  <main class="card">
    <h1>Actualización de información personal</h1>
    <p class="lead">
      Por favor verifica y completa tu información. Los campos en lectura corresponden a datos básicos registrados.
    </p>

    <form method="POST" action="#" novalidate>
      @csrf

      <input type="hidden" name="proceso_id" value="{{ $proceso_id ?? '' }}">
      <input type="hidden" name="codigo" value="{{ $codigo ?? '' }}">

      <div class="grid grid-2">
        <div>
          <label>Nombres</label>
          <input value="{{ old('nombres', $get('nombres')) }}" readonly>
        </div>
        <div>
          <label>Documento</label>
          <input value="{{ old('documento', $get('documento')) }}" readonly>
        </div>
      </div>

      <div class="divider"></div>

      <div class="grid grid-2">
        <div>
          <label>Correo institucional</label>
          <input value="{{ old('correo_institucional', $get('email_institucional')) }}" readonly>
          <div class="help">Este correo no se puede editar.</div>
        </div>
        <div>
          <label for="correo">Correo personal</label>
          <input id="correo" name="correo" type="email"
                 value="{{ old('correo', $get('correo_personal', $get('correo'))) }}"
                 placeholder="tucorreo@dominio.com">
        </div>
      </div>

      <div class="grid grid-3" style="margin-top:12px">
        <div>
          <label for="telefono">Teléfono fijo</label>
          <input id="telefono" name="telefono" value="{{ old('telefono', $get('telefono')) }}" placeholder="Ej: 601234567">
        </div>
        <div>
          <label for="celular">Celular</label>
          <input id="celular" name="celular" value="{{ old('celular', $get('celular')) }}" placeholder="Ej: 3001234567">
        </div>
        <div>
          <label for="direccion">Dirección</label>
          <input id="direccion" name="direccion" value="{{ old('direccion', $get('direccion')) }}">
        </div>
      </div>

      <div class="grid grid-3" style="margin-top:12px">
        <div>
          <label for="ciudad">Ciudad</label>
          <input id="ciudad" name="ciudad" value="{{ old('ciudad', $get('ciudad')) }}">
        </div>
        <div>
          <label for="departamento">Departamento</label>
          <input id="departamento" name="departamento" value="{{ old('departamento', $get('departamento')) }}">
        </div>
        <div>
          <label for="estrato">Estrato</label>
          @php $estratoSel = old('estrato', $get('estrato')); @endphp
          <select id="estrato" name="estrato">
            <option value="">Seleccione…</option>
            @for($i=1;$i<=6;$i++)
              <option value="{{ $i }}" {{ (string)$estratoSel === (string)$i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
          </select>
        </div>
      </div>

      <div class="divider"></div>

      <div class="grid grid-3">
        <div>
          <label for="genero">Género</label>
          @php $genero = old('genero', $get('genero')); @endphp
          <select id="genero" name="genero">
            <option value="">Seleccione…</option>
            <option value="F" {{ $genero==='F' ? 'selected' : '' }}>Femenino</option>
            <option value="M" {{ $genero==='M' ? 'selected' : '' }}>Masculino</option>
            <option value="X" {{ $genero==='X' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
          </select>
        </div>
        <div>
          <label for="fecha_nacimiento">Fecha de nacimiento</label>
          <input id="fecha_nacimiento" name="fecha_nacimiento" type="date"
                 value="{{ old('fecha_nacimiento', $get('fecha_nacimiento')) }}">
        </div>
        <div>
          <label for="rh">Grupo sanguíneo (RH)</label>
          <input id="rh" name="rh" value="{{ old('rh', $get('rh')) }}" placeholder="Ej: O+">
        </div>
      </div>

      <div class="actions">
        <a href="https://www.unicolmayor.edu.co" class="btn btn-secondary">Ir al sitio</a>
        <button type="submit" class="btn btn-primary">Guardar actualización</button>
      </div>
    </form>
  </main>
@endsection

@push('scripts')
<script>
// evita doble envío
document.querySelector('form')?.addEventListener('submit', function(e){
  const btn = this.querySelector('button[type="submit"]');
  if (btn) { btn.disabled = true; btn.textContent = 'Guardando...'; }
});
</script>
@endpush
