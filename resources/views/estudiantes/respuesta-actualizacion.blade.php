{{-- resources/views/estudiantes/respuesta.blade.php --}}
@extends('layouts.ucmc_form_actualizacion')

@section('title','Resultado | Universidad Colegio Mayor de Cundinamarca')

@push('styles')
<style>
  h1{margin:0 0 8px 0;font-size:22px}
  p.lead{margin:0 0 16px 0;color:#374151}
  .section{padding:16px;border:1px solid #e5e7eb;border-radius:12px;background:#fff}
  .section-title{font-weight:700;color:#111827;margin:12px 0 8px}
  .actions{display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end;margin-top:16px}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;border:1px solid transparent;font-weight:600;cursor:pointer;text-decoration:none;font-size:14px}
  .btn-primary{background:var(--ucmc-verde);color:#fff}
  .btn-secondary{background:#e5e7eb;color:#111827}
  .banner{border:1px solid #e5e7eb;border-radius:12px;padding:16px;display:flex;gap:12px;align-items:flex-start}
  .banner--ok{border-color:#86efac;background:#f0fdf4}
  .banner--warn{border-color:#fde68a;background:#fffbeb}
  .banner--err{border-color:#fca5a5;background:#fef2f2}
  .banner__icon{flex:0 0 28px;width:28px;height:28px}
</style>
@endpush

@section('content')
  @php
    /**
     * Soporta ambas formas de entrada:
     * - guardar:      ['code' => int, 'message' => string]
     * - mostrar form: ['estado' => int, 'mensaje' => string]
     */
    $code    = $code    ?? ($estado ?? 200);
    $message = $message ?? ($mensaje ?? 'Operación realizada.');

    // Clasifica estilo visual según el código, pero NO lo mostramos en pantalla.
    $cls = 'banner--ok';
    if ($code >= 400 && $code < 500) $cls = 'banner--warn';
    if ($code >= 500) $cls = 'banner--err';
  @endphp

  <main class="card">
    <h1>Resultado</h1>
    <!-- <p class="lead">A continuación verás el estado del proceso.</p> -->

    <div class="section">
      <div class="banner {{ $cls }}">
        {{-- Ícono simple inline, sin dependencias externas --}}
        <svg class="banner__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          @if ($cls === 'banner--ok')
            <path d="M9 12l2 2 4-4" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="9" stroke="#16a34a" stroke-width="2"/>
          @elseif ($cls === 'banner--warn')
            <path d="M12 8v5m0 3h.01" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12 3l9 16H3l9-16z" stroke="#ca8a04" stroke-width="2" fill="none"/>
          @else
            <path d="M15 9l-6 6M9 9l6 6" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="9" stroke="#dc2626" stroke-width="2" fill="none"/>
          @endif
        </svg>

        <div>
          {{-- Solo el mensaje, sin códigos ni payloads --}}
          <div class="section-title" style="margin-top:0">Estado del proceso</div>
          <div>{{ $message }}</div>
        </div>
      </div>

      <!-- <div class="actions" style="margin-top:16px">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
        <a href="https://www.universidadmayor.edu.co" class="btn btn-primary">Ir al sitio</a>
      </div> -->
    </div>
  </main>
@endsection
