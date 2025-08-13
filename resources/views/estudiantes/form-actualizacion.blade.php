@php
    $row = [];
    if (isset($estudiante)) {
        if (is_array($estudiante) && array_key_exists(0, $estudiante)) {
            $row = is_object($estudiante[0]) ? get_object_vars($estudiante[0]) : (array) $estudiante[0];
        } elseif (is_object($estudiante)) {
            $row = get_object_vars($estudiante);
        } elseif (is_array($estudiante)) {
            $row = $estudiante;
        }
    }

    $allowedGenero = ['F','M','X'];
    $generoRaw  = strtoupper(trim((string) data_get($row, 'genero', '')));
    $segundoRaw = strtoupper(trim((string) data_get($row, 'segundo_nombre', '')));

    if (!in_array($generoRaw, $allowedGenero, true) && in_array($segundoRaw, $allowedGenero, true)) {
        $generoTextoOriginal = (string) data_get($row, 'genero', '');
        $row['genero'] = $segundoRaw;
        $row['segundo_nombre'] = $generoTextoOriginal !== ''
            ? ucwords(strtoupper(trim($generoTextoOriginal)))
            : '';
    }

    $get = fn($key, $default = '') => data_get($row, $key, $default);

    $esPostgrado = (bool) (
        ($esPostgrado ?? null)
        ?? data_get($row, 'esPostgrado', false)
        ?? data_get($row, 'es_postgrado', false)
    );
@endphp



@extends('layouts.ucmc_form_actualizacion')

@section('title','Actualización de datos | Universidad Colegio Mayor de Cundinamarca')

@push('styles')
<style>
  h1{margin:0 0 8px 0;font-size:22px}
  p.lead{margin:0 0 16px 0;color:#374151}
  .grid{
    display:grid;
    column-gap:12px; 
    row-gap:18px;    
    }
  @media (min-width:640px){ .grid-2{grid-template-columns:repeat(2,1fr)} }
  @media (min-width:940px){ .grid-3{grid-template-columns:repeat(3,1fr)} }
  label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}
  .req::after{content:" *"; color:#b91c1c; font-weight:700}
  input,select,textarea{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:#fff;color:#111827}
  input[readonly]{background:#f9fafb;color:#4b5563}
  .help{font-size:12px;color:#6b7280;margin-top:4px}
  .actions{display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end;margin-top:16px}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;border:1px solid transparent;font-weight:600;cursor:pointer;text-decoration:none;font-size:14px}
  .btn-primary{background:var(--ucmc-verde);color:#fff}
  .btn-secondary{background:#e5e7eb;color:#111827}
  .btn:hover{filter:brightness(0.96)}
  .divider{height:1px;background:#e5e7eb;margin:16px 0}
  .section-title{font-weight:700;color:#111827;margin:12px 0 8px}
  .muted{color:#6b7280;font-size:13px}
</style>
@endpush

@section('content')
  <main class="card">
    <h1>Actualización de información personal</h1>
    <p class="lead">
      Por favor verifica y completa tu información. Los campos marcados con <span style="color:#b91c1c">*</span> son obligatorios.
    </p>

    {{-- Para archivos --}}
    <form method="POST" action="#" enctype="multipart/form-data" novalidate>
      @csrf

      {{-- Contexto --}}
      <input type="hidden" name="proceso_id" value="{{ $proceso_id ?? '' }}">
      <input type="hidden" name="codigo" value="{{ $codigo ?? '' }}">
      <input type="hidden" id="es_postgrado_input" name="es_postgrado" value="{{ $esPostgrado ? 1 : 0 }}">

      {{-- Identificación personal --}}
      <div class="section-title">Identificación</div>
      <div class="grid grid-2">
        <div>
          <label class="req" for="primer_nombre">Primer Nombre</label>
          <input id="primer_nombre" name="primer_nombre" value="{{ old('primer_nombre', $get('primer_nombre')) }}" required>
        </div>
        <div>
          <label for="segundo_nombre">Segundo Nombre</label>
          <input id="segundo_nombre" name="segundo_nombre" value="{{ old('segundo_nombre', $get('segundo_nombre')) }}">
        </div>
      </div>
      <div class="grid grid-2" style="margin-top:12px">
        <div>
          <label class="req" for="primer_apellido">Primer Apellido</label>
          <input id="primer_apellido" name="primer_apellido" value="{{ old('primer_apellido', $get('primer_apellido')) }}" required>
        </div>
        <div>
          <label for="segundo_apellido">Segundo Apellido</label>
          <input id="segundo_apellido" name="segundo_apellido" value="{{ old('segundo_apellido', $get('segundo_apellido')) }}">
        </div>
      </div>

      <div class="grid grid-3" style="margin-top:12px">
        <div>
            <label class="req" for="tipo_documento">Tipo de documento</label>
            @php $tipoDoc = old('tipo_documento', $get('tipo_documento_id')); @endphp
            <select id="tipo_documento" name="tipo_documento" required>
                <option value="">Seleccione…</option>
                <option value="1"   {{ $tipoDoc == '1' ? 'selected' : '' }}>Cédula de ciudadanía colombiana</option>
                <option value="2"   {{ $tipoDoc == '2' ? 'selected' : '' }}>Tarjeta de identidad</option>
                <option value="3"   {{ $tipoDoc == '3' ? 'selected' : '' }}>Cédula de extranjería</option>
                <option value="192" {{ $tipoDoc == '192' ? 'selected' : '' }}>Pasaporte</option>
                <option value="71"  {{ $tipoDoc == '71' ? 'selected' : '' }}>Número identificación tributaria</option>
                <option value="0"   {{ $tipoDoc == '0' ? 'selected' : '' }}>Sin documento</option>
                <option value="315" {{ $tipoDoc == '315' ? 'selected' : '' }}>Contraseña</option>
                <option value="375" {{ $tipoDoc == '375' ? 'selected' : '' }}>Visa</option>
                <option value="376" {{ $tipoDoc == '376' ? 'selected' : '' }}>PPT - Permiso por Protección Temporal</option>
            </select>
        </div>

        <div>
          <label class="req" for="numero_documento">Número de documento</label>
          <input id="numero_documento" name="numero_documento" value="{{ old('numero_documento', $get('documento')) }}" required>
        </div>
        <div>
          <label class="req" for="lugar_expedicion">Lugar de expedición</label>
          <input id="lugar_expedicion" name="lugar_expedicion" value="{{ old('lugar_expedicion', $get('lugar_expedicion')) }}" required>
        </div>
      </div>

      <div class="grid grid-3" style="margin-top:12px">
        <div>
            <label class="req" for="genero">Género</label>
            @php $generoSel = old('genero', $get('genero')); @endphp
            <select id="genero" name="genero" required>
                <option value="">Seleccione…</option>
                <option value="F" {{ $generoSel === 'F' ? 'selected' : '' }}>Femenino</option>
                <option value="M" {{ $generoSel === 'M' ? 'selected' : '' }}>Masculino</option>
                <option value="X" {{ $generoSel === 'X' ? 'selected' : '' }}>No binario / Otro</option>
            </select>
        </div>
        <div>
          <label class="req" for="grupo_investigacion">¿Pertenece a grupo de investigación?</label>
          @php $grupo = old('grupo_investigacion', $get('grupo_investigacion')); @endphp
          <select id="grupo_investigacion" name="grupo_investigacion" required>
            <option value="">Seleccione…</option>
            <option value="SI" {{ $grupo==='SI' ? 'selected' : '' }}>Sí</option>
            <option value="NO" {{ $grupo==='NO' ? 'selected' : '' }}>No</option>
          </select>
        </div>
        <div>
          <label class="req" for="telefono">Número de teléfono</label>
          <input id="telefono" name="telefono" value="{{ old('telefono', $get('telefono')) }}" placeholder="Ej: 3001234567" required>
        </div>
      </div>

      {{-- Condicional: nombre del grupo (obligatorio si eligió "SI") --}}
      @php $nombreGrupoOld = old('nombre_grupo', $get('nombre_grupo')); @endphp
      <div id="wrap_nombre_grupo" class="grid" style="margin-top:12px; {{ (old('grupo_investigacion', $grupo) === 'SI') ? '' : 'display:none' }}">
        <div>
          <label class="req" for="nombre_grupo">Nombre del grupo de investigación</label>
          <input id="nombre_grupo" name="nombre_grupo" value="{{ $nombreGrupoOld }}">
        </div>
      </div>

      <div class="grid grid-2" style="margin-top:12px">
        <div>
            <label class="req" for="correo_institucional">Correo electrónico institucional</label>
            <input id="correo_institucional" name="correo_institucional"
                value="{{ old('correo_institucional', $get('email_institucional')) }}" required>
            <div class="help">Sugerencia: mantener el correo institucional activo para notificaciones oficiales.</div>
        </div>
        <div>
          <label for="correo_personal">Correo electrónico personal</label>
          <input id="correo_personal" name="correo_personal"
                 value="{{ old('correo_personal', $get('correo_personal', $get('correo'))) }}">
        </div>
      </div>

      <div class="divider"></div>

      {{-- Vínculos con la institución --}}
      <div class="section-title">Vínculos con la institución</div>
      <div class="grid grid-2">
        @php
          $hijo_func = old('hijo_funcionario', $get('hijo_funcionario'));
          $hijo_doc  = old('hijo_docente', $get('hijo_docente'));
          $es_func   = old('es_funcionario', $get('es_funcionario'));
          $es_doc    = old('es_docente', $get('es_docente'));
        @endphp
        <div>
          <label for="hijo_funcionario">¿Es hijo(a) de funcionario?</label>
          <select id="hijo_funcionario" name="hijo_funcionario">
            <option value="">Seleccione…</option>
            <option value="SI" {{ $hijo_func==='SI' ? 'selected' : '' }}>Sí</option>
            <option value="NO" {{ $hijo_func==='NO' ? 'selected' : '' }}>No</option>
          </select>
        </div>
        <div>
          <label for="hijo_docente">¿Es hijo(a) de docente?</label>
          <select id="hijo_docente" name="hijo_docente">
            <option value="">Seleccione…</option>
            <option value="SI" {{ $hijo_doc==='SI' ? 'selected' : '' }}>Sí</option>
            <option value="NO" {{ $hijo_doc==='NO' ? 'selected' : '' }}>No</option>
          </select>
        </div>
      </div>
      <div class="grid grid-2" style="margin-top:12px">
        <div>
          <label for="es_funcionario">¿Es funcionario de la Universidad?</label>
          <select id="es_funcionario" name="es_funcionario">
            <option value="">Seleccione…</option>
            <option value="SI" {{ $es_func==='SI' ? 'selected' : '' }}>Sí</option>
            <option value="NO" {{ $es_func==='NO' ? 'selected' : '' }}>No</option>
          </select>
        </div>
        <div>
          <label for="es_docente">¿Es docente de la Universidad?</label>
          <select id="es_docente" name="es_docente">
            <option value="">Seleccione…</option>
            <option value="SI" {{ $es_doc==='SI' ? 'selected' : '' }}>Sí</option>
            <option value="NO" {{ $es_doc==='NO' ? 'selected' : '' }}>No</option>
          </select>
        </div>
      </div>

      @if ($esPostgrado)
      <div class="divider"></div>
      
      {{-- Sección posgrado (condicional) --}}
      <div id="seccion_posgrado">
        <div class="section-title">Información de pregrado (para posgrado)</div>
        <div class="grid grid-3">
          <div>
            <label for="titulo_pregrado">Título de pregrado</label>
            <input id="titulo_pregrado" name="titulo_pregrado" value="{{ old('titulo_pregrado', $get('titulo_pregrado')) }}">
          </div>
          <div>
            <label for="universidad_pregrado">Universidad de egreso (pregrado)</label>
            <input id="universidad_pregrado" name="universidad_pregrado" value="{{ old('universidad_pregrado', $get('universidad_pregrado')) }}">
          </div>
          <div>
            <label for="fecha_grado_pregrado">Fecha de grado (pregrado)</label>
            <input id="fecha_grado_pregrado" name="fecha_grado_pregrado" type="date" value="{{ old('fecha_grado_pregrado', $get('fecha_grado_pregrado')) }}">
          </div>
        </div>
      </div>
      @endif


      <div class="divider"></div>

      {{-- Anexos --}}
      <div class="section-title">Anexos</div>
      <div class="grid grid-2">
        <div>
          <label class="req" for="doc_identificacion">Documento de identificación (PDF/JPG/PNG)</label>
          <input id="doc_identificacion" name="doc_identificacion" type="file" accept=".pdf,.jpg,.jpeg,.png" {{ $get('doc_identificacion') ? '' : 'required' }}>
          <div class="help">Obligatorio para corroboración. Tamaño máx. recomendado: 3 MB.</div>
        </div>
        <div>
          <label for="codigo_saber">Código SaberPro o TYT</label>
          <input id="codigo_saber" name="codigo_saber" value="{{ old('codigo_saber', $get('codigo_saber')) }}">
        </div>
      </div>

      <div class="grid grid-2" style="margin-top:12px">
        <div>
          <label for="cert_saber">Certificado de asistencia a SaberPro/TYT (PDF)</label>
          <input id="cert_saber" name="cert_saber" type="file" accept=".pdf">
          <div class="help">Este documento es obligatorio; puede anexarse más adelante, antes de la fecha de graduación.</div>
        </div>
        <div class="muted" style="align-self:end">
          Nota: Estos documentos son obligatorios para cumplir con los requisitos de grado; pueden anexarse más adelante, antes de la fecha de graduación.
        </div>
      </div>

      <div class="actions">
        <a href="https://www.universidadmayor.edu.co" class="btn btn-secondary">Ir al sitio</a>
        <button type="submit" class="btn btn-primary">Guardar actualización</button>
      </div>
    </form>
  </main>
@endsection

@push('scripts')
<script>
// Mostrar/ocultar nombre del grupo de investigación y manejar 'required'
(function(){
  const selGrupo = document.getElementById('grupo_investigacion');
  const wrap = document.getElementById('wrap_nombre_grupo');
  const inputNombre = document.getElementById('nombre_grupo');

  function toggleNombreGrupo(){
    const si = selGrupo && selGrupo.value === 'SI';
    if (!wrap || !inputNombre) return;
    wrap.style.display = si ? '' : 'none';
    inputNombre.required = !!si;
  }

  selGrupo?.addEventListener('change', toggleNombreGrupo);
  toggleNombreGrupo();
})();

// Validación tamaño archivo (3 MB) para doc_identificacion
(function(){
  const MAX = 3 * 1024 * 1024; // 3 MB
  const input = document.getElementById('doc_identificacion');
  input?.addEventListener('change', function(){
    const f = this.files?.[0];
    if (f && f.size > MAX) {
      alert('El archivo supera los 3 MB. Por favor adjunta un archivo de máximo 3 MB.');
      this.value = '';
    }
  });
})();

// Evitar doble envío
document.querySelector('form')?.addEventListener('submit', function(){
  const btn = this.querySelector('button[type="submit"]');
  if (btn) { btn.disabled = true; btn.textContent = 'Guardando...'; }
});
</script>
@endpush
