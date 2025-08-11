@if ($errors->any())
  <div class="card" style="background:#fef2f2;border-color:#fecaca;color:#991b1b">
    <strong>Revisa los campos:</strong>
    <ul style="margin:8px 0 0 18px;padding:0;">
      @foreach ($errors->all() as $error)
        <li style="margin:4px 0">{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if (session('status'))
  <div class="card" style="background:#ecfdf5;border-color:#a7f3d0;color:#065f46">
    {{ session('status') }}
  </div>
@endif
