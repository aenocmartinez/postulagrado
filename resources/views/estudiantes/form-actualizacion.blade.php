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

    // Corrección de género mal ubicado en 'segundo_nombre'
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
<style>
  h1{margin:0 0 8px 0;font-size:22px}
  p.lead{margin:0 0 16px 0;color:#374151}

  .grid{ display:grid; column-gap:12px; row-gap:18px; }
  @media (min-width:640px){ .grid-2{grid-template-columns:repeat(2,1fr)} }
  @media (min-width:940px){ .grid-3{grid-template-columns:repeat(3,1fr)} }

  label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}
  .req::after{content:" *"; color:#b91c1c; font-weight:700}

  input,select,textarea{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:#fff;color:#111827}
  input[readonly]{background:#f9fafb;color:#4b5563}
  select[disabled]{background:#f9fafb;color:#4b5563;pointer-events:none;cursor:not-allowed}

  .help{font-size:12px;color:#6b7280;margin-top:4px}
  .actions{display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end;margin-top:16px}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;border:1px solid transparent;font-weight:600;cursor:pointer;text-decoration:none;font-size:14px}
  .btn-primary{background:var(--ucmc-verde);color:#fff}
  .btn-secondary{background:#e5e7eb;color:#111827}
  .btn:hover{filter:brightness(0.96)}
  .divider{height:1px;background:#e5e7eb;margin:16px 0}
  .section-title{font-weight:700;color:#111827;margin:12px 0 8px}
  .muted{color:#6b7280;font-size:13px}

  /* Jerarquía visual */
  .section{padding:16px;border:1px solid #e5e7eb;border-radius:12px;background:#fff}
  .section-muted{background:#f9fafb;border-color:#e5e7eb}
  .section-muted label{color:#6b7280;font-weight:600}
  .section-muted .field-note{font-size:12px;color:#9ca3af;margin-top:6px}

  /* Enfasis PRIORIDAD (azul suave) */
  .section-emphasis{border:1px solid #bfdbfe;background:#eff6ff;box-shadow:0 1px 0 rgba(59,130,246,.15)}
  .badge{display:inline-block;font-size:11px;font-weight:700;padding:2px 8px;border-radius:9999px;background:#3b82f6;color:#fff;margin-left:8px}

  /* Tom Select ancho completo */
  .ts-wrapper.single .ts-control{border-radius:8px;padding:6px 8px}

</style>

<style>
  /* Oculta el <select> nativo cuando Tom Select lo envuelve */
  select.ts-hidden-accessible{
    position:absolute !important; left:-10000px !important;
    width:1px !important; height:1px !important; overflow:hidden !important;
    padding:0 !important; margin:0 !important; border:0 !important;
    display:block !important; opacity:0 !important;
  }

  /* Fallback mínimo para Tom Select */
  .ts-wrapper{position:relative}
  .ts-control{
    display:flex; align-items:center; flex-wrap:wrap; gap:6px;
    min-height:42px; padding:6px 10px; border:1px solid #d1d5db; border-radius:8px;
    background:#fff;
  }

  /* 🔧 CLAVE: ocultar el input interno del control en modo single */
  .ts-wrapper.single .ts-control input{display:none !important}

  .ts-dropdown{
    position:absolute; top:100%; left:0; right:0; z-index:1000;
    background:#fff; border:1px solid #d1d5db; border-radius:8px;
    max-height:260px; overflow:auto; margin-top:4px; box-shadow:0 8px 20px rgba(0,0,0,.08);
    display:none;
  }
  .ts-wrapper.dropdown-active .ts-dropdown{display:block}

  /* Caja de búsqueda dentro del dropdown (plugin dropdown_input) */
  .ts-dropdown .dropdown-input{padding:8px 10px; border-bottom:1px solid #e5e7eb}
  .ts-dropdown .dropdown-input input{width:100%; border:0; outline:0; font-size:14px; padding:6px 0}

  .ts-dropdown .option{padding:8px 10px}
  .ts-dropdown .option.active{background:#eff6ff}
  .ts-dropdown .no-results{padding:8px 10px; color:#6b7280}
</style>

@endpush

@section('content')
  <main class="card">
    <h1>Actualización de información personal</h1>
    <p class="lead">
      Verifica y completa tu información. Algunos datos de identificación están en solo lectura para que la actualización se realice con base en el
      <strong>documento de identificación que adjuntas</strong>. Si requieren corrección, por favor contacta a la oficina correspondiente.
    </p>

    <form method="POST" action="#" enctype="multipart/form-data" novalidate>
      @csrf
      <input type="hidden" name="proceso_id" value="{{ $proceso_id ?? '' }}">
      <input type="hidden" name="codigo" value="{{ $codigo ?? '' }}">
      <input type="hidden" id="es_postgrado_input" name="es_postgrado" value="{{ $esPostgrado ? 1 : 0 }}">

      {{-- IDENTIFICACIÓN (DE-ENFATIZADA / SOLO LECTURA) --}}
      <div class="section section-muted">
        <div class="section-title">Identificación</div>

        <div class="grid grid-2">
          <div>
            <label for="primer_nombre">Primer Nombre</label>
            <input id="primer_nombre" name="primer_nombre" value="{{ old('primer_nombre', $get('primer_nombre')) }}" readonly>
          </div>
          <div>
            <label for="segundo_nombre">Segundo Nombre</label>
            <input id="segundo_nombre" name="segundo_nombre" value="{{ old('segundo_nombre', $get('segundo_nombre')) }}" readonly>
          </div>
        </div>

        <div class="grid grid-2" style="margin-top:12px">
          <div>
            <label for="primer_apellido">Primer Apellido</label>
            <input id="primer_apellido" name="primer_apellido" value="{{ old('primer_apellido', $get('primer_apellido')) }}" readonly>
          </div>
          <div>
            <label for="segundo_apellido">Segundo Apellido</label>
            <input id="segundo_apellido" name="segundo_apellido" value="{{ old('segundo_apellido', $get('segundo_apellido')) }}" readonly>
          </div>
        </div>

        @php $tipoDoc = old('tipo_documento', $get('tipo_documento_id')); @endphp
        <div class="grid grid-3" style="margin-top:12px">
          <div>
              <label for="tipo_documento">Tipo de documento</label>
              <select id="tipo_documento" name="tipo_documento" disabled>
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
              <input type="hidden" name="tipo_documento" value="{{ $tipoDoc }}">
          </div>

          <div>
            <label for="numero_documento">Número de documento</label>
            <input id="numero_documento" name="numero_documento" value="{{ old('numero_documento', $get('documento')) }}" readonly>
          </div>
          <div>
            <label for="lugar_expedicion">Lugar de expedición</label>
            <input id="lugar_expedicion" name="lugar_expedicion" value="{{ old('lugar_expedicion', $get('lugar_expedicion')) }}" readonly>
          </div>
        </div>

        @php $generoSel = old('genero', $get('genero')); @endphp
        <div class="grid grid-2" style="margin-top:12px">
          <div>
            <label for="genero">Género</label>
            <select id="genero" name="genero" disabled>
              <option value="">Seleccione…</option>
              <option value="F" {{ $generoSel === 'F' ? 'selected' : '' }}>Femenino</option>
              <option value="M" {{ $generoSel === 'M' ? 'selected' : '' }}>Masculino</option>
              <option value="X" {{ $generoSel === 'X' ? 'selected' : '' }}>No binario / Otro</option>
            </select>
            <input type="hidden" name="genero" value="{{ $generoSel }}">
          </div>
          <div>
            <label for="correo_institucional">Correo electrónico institucional</label>
            <input id="correo_institucional" name="correo_institucional"
                   value="{{ old('correo_institucional', $get('email_institucional')) }}" readonly>
          </div>
        </div>

        <div class="field-note">
          Estos datos están bloqueados porque se validarán y actualizarán según el documento de identificación que adjuntes.
          Si observas alguna inconsistencia, comunícate con Soporte Académico.
        </div>
      </div>

      <div class="divider"></div>

      {{-- ANEXOS PRIORITARIOS (AZUL SUAVE) --}}
      <div class="section section-emphasis">
        <div class="section-title">
          Anexos prioritarios <span class="badge">PRIORITARIO</span>
        </div>

        {{-- Documento de identificación ocupa todo el ancho --}}
        <div class="grid">
          <div>
            <label class="req" for="doc_identificacion">Documento de identificación (PDF/JPG/PNG)</label>
            <input id="doc_identificacion" name="doc_identificacion" type="file" accept=".pdf,.jpg,.jpeg,.png" {{ $get('doc_identificacion') ? '' : 'required' }}>
            <div class="help">Debe adjuntarse para corroboración de identidad. Tamaño máximo recomendado: 3 MB.</div>
          </div>
        </div>

        {{-- Certificado y Código, lado a lado, con una sola nota unificada --}}
        <div class="grid grid-2" style="margin-top:12px">
          <div>
            <label for="cert_saber">Certificado de asistencia a SaberPro/TYT (PDF)</label>
            <input id="cert_saber" name="cert_saber" type="file" accept=".pdf">
          </div>
          <div>
            <label for="codigo_saber">Código SaberPro o TYT</label>
            <input id="codigo_saber" name="codigo_saber" value="{{ old('codigo_saber', $get('codigo_saber')) }}">
          </div>
        </div>
        <div class="help" style="margin-top:6px">
          Estos documentos pueden anexarse más adelante, antes de la fecha de graduación.
        </div>
      </div>

      <div class="divider"></div>

      {{-- CONTACTO Y VÍNCULOS --}}
      <div class="section">
        <div class="section-title">Contacto y vínculos</div>

        <div class="grid grid-3">
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
          <div>
            <label for="correo_personal">Correo electrónico personal</label>
            <input id="correo_personal" name="correo_personal" value="{{ old('correo_personal', $get('correo_personal', $get('correo'))) }}">
          </div>
        </div>

        {{-- Nombre del grupo (condicional) --}}
        @php $nombreGrupoOld = old('nombre_grupo', $get('nombre_grupo')); @endphp
        <div id="wrap_nombre_grupo" class="grid" style="margin-top:12px; {{ (old('grupo_investigacion', $grupo) === 'SI') ? '' : 'display:none' }}">
          <div>
            <label class="req" for="nombre_grupo">Nombre del grupo de investigación</label>
            <input id="nombre_grupo" name="nombre_grupo" value="{{ $nombreGrupoOld }}">
          </div>
        </div>

        <div class="divider"></div>

        <div class="section-title">Vínculos con la institución</div>
        @php
          $hijo_func = old('hijo_funcionario', $get('hijo_funcionario'));
          $hijo_doc  = old('hijo_docente', $get('hijo_docente'));
          $es_func   = old('es_funcionario', $get('es_funcionario'));
          $es_doc    = old('es_docente', $get('es_docente'));
        @endphp

        <div class="grid grid-2">
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
      </div>

      @if ($esPostgrado)
      <div class="divider"></div>

      {{-- POSGRADO --}}
@php
    $listaUniversidades = [
        'Universidad Colegio Mayor de Cundinamarca',
        'Universidad Nacional de Colombia',
        'Universidad de los Andes',
        'Pontificia Universidad Javeriana',
        'Universidad del Rosario',
        'Universidad Externado de Colombia',
        'Universidad de La Sabana',
        'Universidad de Antioquia',
        'Universidad del Valle',
        'Universidad Industrial de Santander',
        'Universidad EAFIT',
        'Universidad Pontificia Bolivariana',
        'Universidad del Norte',
        'Universidad Distrital Francisco José de Caldas',
        'Universidad Pedagógica Nacional',
        'Universidad Tecnológica de Pereira',
        'Universidad de Caldas',
        'Universidad de Cartagena',
        'Universidad Sergio Arboleda',
        'Universidad de Manizales',
    ];
    $uOld    = trim(old('universidad_pregrado', $get('universidad_pregrado')));
    $enLista = in_array($uOld, $listaUniversidades, true);
    $usaOtra = ($uOld !== '' && !$enLista);
@endphp

      <div class="section">
        <div class="section-title">Información adicional (postgrado)</div>
        <div class="grid grid-3">
          <div>
            <label for="titulo_pregrado">Título de pregrado</label>
            <input id="titulo_pregrado" name="titulo_pregrado" value="{{ old('titulo_pregrado', $get('titulo_pregrado')) }}">
          </div>

<div>
  <label for="universidad_pregrado_select">Universidad de egreso (pregrado)</label>

  <!-- Valor FINAL para el backend -->
  <input type="hidden" id="universidad_pregrado" name="universidad_pregrado" value="{{ $uOld }}">

  <!-- Select con búsqueda (sin name para no postear duplicado) -->
  <select id="universidad_pregrado_select" placeholder="Seleccione…" autocomplete="off">
    <option value="" {{ $uOld === '' ? 'selected' : '' }}>Seleccione…</option>
    @foreach ($listaUniversidades as $u)
      <option value="{{ $u }}" {{ $enLista && $uOld === $u ? 'selected' : '' }}>{{ $u }}</option>
    @endforeach
    <option value="__OTRA__" {{ $usaOtra ? 'selected' : '' }}>Otra (¿cuál?)</option>
  </select>

  <!-- “Otra, ¿cuál?” SOLO si se elige la opción anterior -->
  <div id="wrap_universidad_otro" style="{{ $usaOtra ? '' : 'display:none' }}">
    <input id="universidad_pregrado_otro"
           placeholder="Especifique la universidad"
           value="{{ $usaOtra ? $uOld : '' }}">
    <div class="help">Si no aparece en la lista, elige “Otra (¿cuál?)” y escribe el nombre completo.</div>
  </div>
</div>

          <div>
            <label for="fecha_grado_pregrado">Fecha de grado (pregrado)</label>
            <input id="fecha_grado_pregrado" name="fecha_grado_pregrado" type="date" value="{{ old('fecha_grado_pregrado', $get('fecha_grado_pregrado')) }}">
          </div>
        </div>
      </div>
      @endif

      <div class="actions">
        <a href="https://www.universidadmayor.edu.co" class="btn btn-secondary">Ir al sitio</a>
        <button type="submit" class="btn btn-primary">Guardar actualización</button>
      </div>
    </form>
  </main>
@endsection

@push('scripts')
<script>
// Mostrar/ocultar "Nombre del grupo" y manejar required dinámico
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

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
(function(){
  const OTRA_VAL  = '__OTRA__';
  const sel       = document.getElementById('universidad_pregrado_select');
  const hid       = document.getElementById('universidad_pregrado');
  const wrapOtro  = document.getElementById('wrap_universidad_otro');
  const inputOtro = document.getElementById('universidad_pregrado_otro');
  const form      = document.querySelector('form');

  if (!sel) return;

  // Evita dobles inicializaciones si el script se carga de nuevo
  if (sel.tomselect) { sel.tomselect.destroy(); }

  const ts = new TomSelect(sel, {
    create: false,                 // usamos la opción “OTRA” explícita
    allowEmptyOption: true,
    maxOptions: 10000,
    searchField: ['text','value'],
    sortField: { field: 'text', direction: 'asc' },
    placeholder: sel.getAttribute('placeholder') || 'Seleccione…',
    plugins: ['dropdown_input']    // 🔧 búsqueda dentro del menú (no en el control)
  });

  function toggleOtra(val){
    const esOtra = (val === OTRA_VAL);
    if (wrapOtro) wrapOtro.style.display = esOtra ? '' : 'none';
    if (inputOtro) inputOtro.required = esOtra;
  }

  function syncHidden(){
    const v = ts.getValue();
    if (v === OTRA_VAL) {
      hid.value = (inputOtro?.value || '').trim();
    } else {
      hid.value = v || '';
    }
    toggleOtra(v);
  }

  // Estado inicial
  syncHidden();

  // Actualiza al cambiar la selección
  ts.on('change', syncHidden);

  // Actualiza al escribir “Otra, ¿cuál?”
  inputOtro?.addEventListener('input', syncHidden);

  // Valida antes de enviar
  form?.addEventListener('submit', function(e){
    if (ts.getValue() === OTRA_VAL) {
      const texto = (inputOtro?.value || '').trim();
      if (!texto) {
        e.preventDefault();
        inputOtro?.focus();
        alert('Por favor escribe el nombre de la universidad.');
        return;
      }
      hid.value = texto;
    }
  });
})();
</script>


@endpush
