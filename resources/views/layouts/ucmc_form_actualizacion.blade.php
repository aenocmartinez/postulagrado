<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title','Universidad Colegio Mayor de Cundinamarca')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root{ --ucmc-azul:#0B3D91; --ucmc-cian:#0ea5e9; --ucmc-verde:#16a34a; --ucmc-gris:#6b7280; --ucmc-bg:#f3f4f6; }
    *{box-sizing:border-box}
    body{margin:0;background:var(--ucmc-bg);color:#111827;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif}
    .container{max-width:980px;margin:24px auto;padding:0 16px}
    .header{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px}
    .brand{display:flex;align-items:center;gap:12px}
    .brand-title{font-weight:700;color:#111827;font-size:16px;line-height:1.2}
    .badge-wrap{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-top:16px}
    .footer{color:var(--ucmc-gris);font-size:12px;text-align:center;margin:18px 0}
    .logo{height:48px}
    .sello{height:46px}
    @stack('styles')
  </style>
</head>
<body>
  <div class="container">
    {{-- Encabezado institucional --}}
    <header class="header">
      <div class="brand">
        <img class="logo" src="{{ asset('images/Logo-Universidad-2.png') }}" alt="Universidad Colegio Mayor de Cundinamarca">
        <div class="brand-title">
          Universidad Colegio Mayor de Cundinamarca<br>
          <span style="font-weight:500;color:var(--ucmc-gris);font-size:13px;">Proceso de postulación a grado</span>
        </div>
      </div>
      <div class="badge-wrap">
        <img class="sello" src="{{ asset('images/LogoAcreCalidadLegal.png') }}" alt="Acreditación en Alta Calidad">
        <img class="sello" src="{{ asset('images/logo-icontec.png') }}" alt="Icontec ISO 9001 / IQNet">
      </div>
    </header>

    {{-- Mensajes globales --}}
    @includeIf('partials.alertas')

    {{-- Contenido específico --}}
    @yield('content')

    {{-- Pie --}}
    <div class="footer">
      © {{ date('Y') }} Universidad Colegio Mayor de Cundinamarca ·
      Sistema de Gestión de Calidad – ISO 9001 · Acreditación de Alta Calidad
    </div>
  </div>

  @stack('scripts')
</body>
</html>
