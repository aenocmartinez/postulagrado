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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
<style>
  h1{margin:0 0 8px 0;font-size:22px}
  p.lead{margin:0 0 16px 0;color:#374151}
  .grid{display:grid;column-gap:12px;row-gap:18px}
  @media (min-width:640px){.grid-2{grid-template-columns:repeat(2,1fr)}}
  @media (min-width:940px){.grid-3{grid-template-columns:repeat(3,1fr)}}
  label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}
  .req::after{content:" *";color:#b91c1c;font-weight:700}
  input,select,textarea{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:#fff;color:#111827}
  input[readonly]{background:#f9fafb;color:#4b5563}
  select[disabled]{background:#f9fafb;color:#4b5563;pointer-events:none;cursor:not-allowed}
  .help{font-size:12px;color:#6b7280;margin-top:4px}
  .actions{display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end;margin-top:16px}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;border:1px solid transparent;font-weight:600;cursor:pointer;text-decoration:none;font-size:14px}
  .btn-primary{background:var(--ucmc-verde);color:#fff}
  .btn-secondary{background:#e5e7eb;color:#111827}
  .btn:hover{filter:brightness(0.96)}
  .divider{height:1px;background:#e5e7eb;margin:16px 0;}
  .divider--spacer{height:0;background:transparent; margin:16px 0;}
  .section-title{font-weight:700;color:#111827;margin:12px 0 8px}
  .muted{color:#6b7280;font-size:13px}
  .section{padding:16px;border:1px solid #e5e7eb;border-radius:12px;background:#fff}
  .section-muted{background:#f9fafb;border-color:#e5e7eb}
  .section-muted label{color:#6b7280;font-weight:600}
  .section-muted .field-note{font-size:12px;color:#9ca3af;margin-top:6px}
  .section-emphasis{border:1px solid #bfdbfe;background:#eff6ff;box-shadow:0 1px 0 rgba(59,130,246,.15)}
  .badge{display:inline-block;font-size:11px;font-weight:700;padding:2px 8px;border-radius:9999px;background:#3b82f6;color:#fff;margin-left:8px}
  select.ts-hidden-accessible{position:absolute!important;left:-10000px!important;width:1px!important;height:1px!important;overflow:hidden!important;padding:0!important;margin:0!important;border:0!important;display:block!important;opacity:0!important}
  .ts-wrapper{position:relative}
  .ts-control{display:flex;align-items:center;flex-wrap:wrap;gap:6px;min-height:42px;padding:6px 10px;border:1px solid #d1d5db;border-radius:8px;background:#fff}
  .ts-wrapper.single .ts-control input{display:none!important}
  .ts-dropdown{position:absolute;top:100%;left:0;right:0;z-index:1000;background:#fff;border:1px solid #d1d5db;border-radius:8px;max-height:260px;overflow:auto;margin-top:4px;box-shadow:0 8px 20px rgba(0,0,0,.08);display:none}
  .ts-wrapper.dropdown-active .ts-dropdown{display:block}
  .ts-dropdown .dropdown-input{padding:8px 10px;border-bottom:1px solid #e5e7eb}
  .ts-dropdown .dropdown-input input{width:100%;border:0;outline:0;font-size:14px;padding:6px 0}
  .ts-dropdown .option{padding:8px 10px}
  .ts-dropdown .option.active{background:#eff6ff}
  .ts-dropdown .no-results{padding:8px 10px;color:#6b7280}
  .file-drop{border:2px dashed #bfdbfe;background:#eff6ff;border-radius:14px;padding:18px;transition:.15s ease-in-out}
  .file-drop:hover{background:#e9f1ff}
  .file-drop.is-drag{background:#dbeafe;border-color:#60a5fa}
  .file-drop__click{display:flex;gap:12px;align-items:center;justify-content:center;cursor:pointer;text-align:center}
  .file-drop__icon{width:28px;height:28px;flex:0 0 28px}
  .file-drop__title{font-weight:700;color:#111827}
  .file-drop__hint{font-size:12px;color:#6b7280}
  .file-drop input[type="file"]{display:none}
  .file-drop__preview{margin-top:10px;display:flex;align-items:center;gap:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:10px}
  .file-drop__thumb{width:36px;height:36px;border-radius:6px;object-fit:cover;border:1px solid #e5e7eb}
  .file-drop__meta{font-size:13px;color:#374151}
  .file-drop__remove{margin-left:auto;border:0;background:#f3f4f6;padding:6px 10px;border-radius:8px;cursor:pointer}
  .file-drop__remove:hover{background:#e5e7eb}
</style>
@endpush

@section('content')
  <main class="card">
    <h1>Actualización de información personal</h1>
    <p class="lead">
      Verifica y completa tu información. Algunos datos de identificación están en solo lectura para que la actualización se realice con base en el
      <strong>documento de identificación que adjuntas</strong>.
    </p>

    <form method="POST" action="{{ route('postulacion.actualizacion.store') }}" enctype="multipart/form-data" novalidate>

      @csrf      
      <input type="hidden" name="enlace_id" value="{{ $enlace_id ?? '' }}">
      <input type="hidden" name="proceso_id" value="{{ $proceso_id ?? '' }}">
      <input type="text" name="codigo" value="{{ $codigo ?? '' }}">
      <input type="hidden" id="es_postgrado_input" name="es_postgrado" value="{{ $esPostgrado ? 1 : 0 }}">

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
            <input id="correo_institucional" name="correo_institucional" value="{{ old('correo_institucional', $get('email_institucional')) }}" readonly>
          </div>
        </div>
        <div class="field-note">Estos datos están bloqueados porque se validarán y actualizarán según el documento de identificación que adjuntes. Si observas alguna inconsistencia, comunícate con Soporte Académico.</div>
      </div>

      <div class="divider divider--spacer"></div>

      <div class="section section-emphasis">
        <div class="section-title">Anexos prioritarios <span class="badge">PRIORITARIO</span></div>
        <div class="grid" style="margin-bottom: 40px;">
          <div>
            <label class="req file-drop__title" for="doc_identificacion" style="text-align: center; font-size:medium">Documento de identificación (PDF/JPG/PNG)</label>
            <div class="file-drop" id="doc_drop" data-max="3145728">
              <div class="file-drop__click" id="doc_click">
                <svg class="file-drop__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M12 16V8m0 0l-3 3m3-3l3 3M4 16a4 4 0 014-4h8a4 4 0 014 4v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div>
                  <div class="file-drop__title">Arrastra y suelta el archivo aquí</div>
                  <div class="file-drop__hint">o haz clic para seleccionarlo desde tu equipo</div>
                </div>
              </div>
              <input id="doc_identificacion" name="doc_identificacion" type="file" accept=".pdf,.jpg,.jpeg,.png" {{ $get('doc_identificacion') ? '' : 'required' }}>
              <div class="file-drop__preview" id="doc_preview" style="display:none">
                <img id="doc_thumb" class="file-drop__thumb" alt="vista previa">
                <div class="file-drop__meta">
                  <div id="doc_name"></div>
                  <div class="file-drop__hint" id="doc_size"></div>
                </div>
                <button type="button" class="file-drop__remove" id="doc_remove">Quitar</button>
              </div>
            </div>
            <div class="help" style="text-align: center;">Debe adjuntarse para corroborar identidad. Tamaño máximo recomendado: 3 MB.</div>
          </div>
        </div>
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
        <div class="help" style="margin-top:6px; text-align: center;">Estos documentos pueden anexarse más adelante, antes de la fecha de graduación.</div>
      </div>

      <div class="divider divider--spacer"></div>

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

        <div class="grid grid-3" style="margin-top:12px">
          @php
            $departamentoOld = old('departamento', $get('departamento'));
            $ciudadOld = old('ciudad', $get('ciudad'));
            $direccionOld = old('direccion', $get('direccion'));
          @endphp
          <div>
            <label class="req" for="departamento">Departamento</label>
            <select id="departamento" name="departamento" required>
              <option value="">Seleccione…</option>
              @php
                $departamentos = [
                  'Amazonas','Antioquia','Arauca','Atlántico','Bogotá D.C.','Bolívar','Boyacá','Caldas','Caquetá','Casanare','Cauca','Cesar','Chocó','Córdoba','Cundinamarca','Guainía','Guaviare','Huila','La Guajira','Magdalena','Meta','Nariño','Norte de Santander','Putumayo','Quindío','Risaralda','San Andrés y Providencia','Santander','Sucre','Tolima','Valle del Cauca','Vaupés','Vichada'
                ];
              @endphp
              @foreach($departamentos as $dep)
                <option value="{{ $dep }}" {{ $departamentoOld===$dep ? 'selected' : '' }}>{{ $dep }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="req" for="ciudad">Ciudad o Municipio</label>
            <select id="ciudad" name="ciudad" required data-old="{{ $ciudadOld }}">
              <option value="">Seleccione…</option>
            </select>
          </div>
          <div>
            <label class="req" for="direccion">Dirección</label>
            <input id="direccion" name="direccion" value="{{ $direccionOld }}" placeholder="Ej: Calle 10 # 5-23 Apto 301" required autocomplete="street-address">
          </div>
        </div>

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
      <div class="divider divider--spacer"></div>
      @php
        $uOld = trim(old('universidad_pregrado', $get('universidad_pregrado')));
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
            <input type="hidden" id="universidad_pregrado" name="universidad_pregrado" value="{{ $uOld }}">
            <select id="universidad_pregrado_select" placeholder="Seleccione…" autocomplete="off">
              <option value="">Seleccione…</option>
              <option value="__OTRA__">Otra (¿cuál?)</option>
            </select>

            <div id="wrap_universidad_otro" style="display:none">
              <input id="universidad_pregrado_otro" placeholder="Especifique la universidad">
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
        <!-- <a href="https://www.universidadmayor.edu.co" class="btn btn-secondary">Ir al sitio</a> -->
        <button type="submit" class="btn btn-primary">Guardar actualización</button>
      </div>
    </form>
  </main>
@endsection

@push('scripts')

<script src="{{ asset('js/colombia-geo.js') }}"></script>
<script src="{{ asset('js/ies-oficial.js') }}" defer></script>


<script>
(function(){
  const selGrupo=document.getElementById('grupo_investigacion');
  const wrap=document.getElementById('wrap_nombre_grupo');
  const inputNombre=document.getElementById('nombre_grupo');
  function toggle(){const si=selGrupo&&selGrupo.value==='SI';if(!wrap||!inputNombre)return;wrap.style.display=si?'':'none';inputNombre.required=!!si}
  selGrupo?.addEventListener('change',toggle);toggle();
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  (function () {
    const dep = document.getElementById('departamento');
    const sel = document.getElementById('ciudad');
    if (!dep || !sel) return;

    let DATA = window.CO_DEPARTAMENTOS || {};

    // normalización para comparar nombres (ignora acentos, comas, puntos y espacios)
    const strip = s => (s||'')
      .normalize('NFD').replace(/\p{Diacritic}/gu,'')
      .replace(/[,\.\s]/g,'').toLowerCase();

    function resolveKey(k){
      if (DATA[k]) return k;
      const nk = strip(k);
      for (const key of Object.keys(DATA)) if (strip(key) === nk) return key;
      for (const key of Object.keys(DATA)) if (strip(key).includes(nk) || nk.includes(strip(key))) return key;
      return k;
    }

    // Title Case en ES (respeta preposiciones comunes; capitaliza cada segmento con guión)
    const LOWER = new Set(['de','del','la','las','los','y','e','o','u','al']);
    function toTitle(s){
      return s.toLowerCase().split(/\s+/).map((w,i)=>{
        if (i>0 && LOWER.has(w)) return w;
        return w.split('-').map(p => p.charAt(0).toUpperCase() + p.slice(1)).join('-');
      }).join(' ');
    }

    function fill() {
      const deptoSel = dep.value || '';

      // Regla 1: “San Andrés y Providencia” → sin ciudades
      if (strip(deptoSel) === strip('San Andrés y Providencia')) {
        sel.innerHTML = '<option value="">No aplica</option>';
        sel.value = '';
        sel.disabled = true;
        sel.required = false; // evita bloquear el envío del form
        return;
      } else {
        sel.disabled = false;
        sel.required = true;
      }

      const key = resolveKey(deptoSel);
      const listado = Array.isArray(DATA[key]) ? DATA[key] : [];
      const uniq = [...new Set(listado)].sort((a,b)=>a.localeCompare(b,'es'));

      sel.innerHTML = '<option value="">Seleccione…</option>';
      const frag = document.createDocumentFragment();
      uniq.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c;                 // valor oficial (puede estar en mayúsculas)
        opt.textContent = toTitle(c);  // texto visible en Title Case
        frag.appendChild(opt);
      });
      sel.appendChild(frag);

      // Si tienes un valor previo (viejo), intenta seleccionarlo
      if (sel.dataset.old && uniq.includes(sel.dataset.old)) {
        sel.value = sel.dataset.old;
      }
    }

    dep.addEventListener('change', fill);
    window.addEventListener('co-departamentos:ready', () => { DATA = window.CO_DEPARTAMENTOS || {}; fill(); });

    fill();
  })();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
(function(){
  const OTRA='__OTRA__';
  const sel=document.getElementById('universidad_pregrado_select');
  const hid=document.getElementById('universidad_pregrado');
  const wrapOtro=document.getElementById('wrap_universidad_otro');
  const inputOtro=document.getElementById('universidad_pregrado_otro');
  const tituloInp=document.getElementById('titulo_pregrado');
  const fechaInp=document.getElementById('fecha_grado_pregrado');
  const tituloLab=document.querySelector('label[for="titulo_pregrado"]');
  const fechaLab=document.querySelector('label[for="fecha_grado_pregrado"]');
  const TARGET='universidad-colegio mayor de cundinamarca';
  if (!sel) return;

  // normalizador para comparar
  const strip = s => (s||'').trim().toLowerCase()
    .normalize('NFD').replace(/\p{Diacritic}/gu,'');

  // opciones base
  function setBaseOptions(){
    sel.innerHTML = '<option value="">Seleccione…</option><option value="'+OTRA+'">Otra (¿cuál?)</option>';
  }

  function populateIES(){
    setBaseOptions();
    const ies = Array.isArray(window.CO_IES) ? window.CO_IES : [];
    const frag = document.createDocumentFragment();
    for (const {value,label} of ies){
      const opt = document.createElement('option');
      opt.value = value;        // valor oficial (MEN/SNIES)
      opt.textContent = label;  // visible en Title Case
      frag.appendChild(opt);
    }
    const last = sel.querySelector('option[value="'+OTRA+'"]');
    sel.insertBefore(frag, last);
  }

  function toggleOtra(val){
    const es = val===OTRA;
    if (wrapOtro) wrapOtro.style.display = es ? '' : 'none';
    if (inputOtro) inputOtro.required = es;
  }

  function currentUni(){
    const v = sel.value;
    return v===OTRA ? (inputOtro?.value||'').trim() : (v||'');
  }

  function enforceRequired(){
    const isUCMC = strip(currentUni()) === strip(TARGET);
    if (tituloInp) tituloInp.required = isUCMC;
    if (fechaInp)  fechaInp.required = isUCMC;
    if (tituloLab) tituloLab.classList.toggle('req', isUCMC);
    if (fechaLab)  fechaLab.classList.toggle('req', isUCMC);
  }

  function sync(){
    const v = sel.value;
    hid.value = (v===OTRA) ? (inputOtro?.value||'').trim() : (v||'');
    toggleOtra(v);
    enforceRequired();
  }

  function bootTomSelect(){
    if (sel.tomselect) sel.tomselect.destroy();
    return new TomSelect(sel,{
      create:false, allowEmptyOption:true, maxOptions:10000,
      searchField:['text','value'], sortField:{field:'text',direction:'asc'},
      placeholder: sel.getAttribute('placeholder')||'Seleccione…',
      plugins:['dropdown_input']
    });
  }

  // Listeners
  sel.addEventListener('change', sync);
  inputOtro?.addEventListener('input', sync);

  // Población con fuente oficial
  window.addEventListener('ies-oficial:ready', ()=>{
    populateIES();
    const ts = bootTomSelect();

    // Restaurar valor previo (hidden)
    const oldRaw = (document.getElementById('universidad_pregrado')?.value || '').trim();
    const old = strip(oldRaw);
    if (old) {
      // intenta match por normalización contra los <option>
      let match = '';
      for (const opt of sel.options) {
        if (opt.value === '' || opt.value === OTRA) continue;
        if (strip(opt.value) === old) { match = opt.value; break; }
      }
      if (match) {
        ts.setValue(match, true);
      } else {
        ts.setValue(OTRA, true);
        if (inputOtro) inputOtro.value = oldRaw;
        if (wrapOtro) wrapOtro.style.display = '';
      }
    }
    sync();
  });

  // base mientras llega la data
  setBaseOptions();
})();
</script>



<script>
(function(){
  const drop=document.getElementById('doc_drop');
  const click=document.getElementById('doc_click');
  const input=document.getElementById('doc_identificacion');
  const prev=document.getElementById('doc_preview');
  const thumb=document.getElementById('doc_thumb');
  const nameEl=document.getElementById('doc_name');
  const sizeEl=document.getElementById('doc_size');
  const rm=document.getElementById('doc_remove');
  const MAX=parseInt(drop?.dataset?.max||(3*1024*1024),10);

  if(!drop||!input)return;

  function bytes(n){return n<1024*1024?(n/1024).toFixed(0)+' KB':(n/1024/1024).toFixed(2)+' MB'}

  function show(file){
    nameEl.textContent=file.name;
    sizeEl.textContent=bytes(file.size);
    if(/image\/(png|jpe?g)/i.test(file.type)){
      const url=URL.createObjectURL(file);
      thumb.src=url; thumb.style.objectFit='cover';
    }else{
      thumb.src='data:image/svg+xml;utf8,<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"><rect width="36" height="36" rx="6" fill="%23f3f4f6"/><text x="50%" y="58%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="12" fill="%236b7280">PDF</text></svg>';
      thumb.style.objectFit='contain';
    }
    prev.style.display='';
  }

  function clear(){ input.value=''; prev.style.display='none'; }

  function setInputFile(file){
    const dt=new DataTransfer();
    dt.items.add(file);
    input.files=dt.files;
  }

  function handleFile(file, setIntoInput){
    if(!file) return;
    if(file.size>MAX){ alert('El archivo supera los 3 MB. Por favor adjunta un archivo de máximo 3 MB.'); clear(); return; }
    if(setIntoInput) setInputFile(file);
    show(file);
  }

  click?.addEventListener('click',()=>input.click());
  input?.addEventListener('change',()=>handleFile(input.files?.[0], false));

  ['dragenter','dragover'].forEach(ev=>drop.addEventListener(ev,e=>{
    e.preventDefault(); e.stopPropagation(); drop.classList.add('is-drag');
  }));
  ['dragleave','dragend','drop'].forEach(ev=>drop.addEventListener(ev,e=>{
    e.preventDefault(); e.stopPropagation(); drop.classList.remove('is-drag');
  }));
  drop.addEventListener('drop',e=>{
    const f=e.dataTransfer?.files?.[0];
    if(f) handleFile(f, true);
  });

  rm?.addEventListener('click',clear);
})();
</script>


<script>
document.querySelector('form')?.addEventListener('submit',function(){
  const btn=this.querySelector('button[type="submit"]');if(btn){btn.disabled=true;btn.textContent='Guardando...'}
});
</script>

<script>
(function(){
  const cert=document.getElementById('cert_saber');
  cert?.addEventListener('change',function(){
    const f=this.files?.[0];
    if(f && f.type!=='application/pdf' && !/\.pdf$/i.test(f.name)){
      alert('El certificado debe ser un archivo PDF.');
      this.value='';
    }
  });
})();
</script>

@endpush
