<tr class="border-b hover:bg-gray-50" data-codigo="{{ $est['estu_codigo'] }}">
  <td class="px-4 py-2">{{ $est['detalle']->pensum_estud ?? '-' }}</td>
  <td class="px-4 py-2">{{ $est['estu_codigo'] }}</td>
  <td class="px-4 py-2">{{ $est['detalle']->documento ?? '-' }}</td>
  <td class="px-4 py-2">{{ $est['detalle']->nombres ?? '-' }}</td>
  <td class="px-4 py-2">{{ $est['detalle']->categoria ?? '-' }}</td>
  <td class="px-4 py-2">
    <span class="inline-block px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
      {{ $est['detalle']->situacion ?? '-' }}
    </span>
  </td>
  <td class="px-2 py-2 text-center">{{ $est['detalle']->cred_pendientes ?? '-' }}</td>
  <td class="px-4 py-2 text-center">
    <div class="flex justify-center gap-2">
      <button data-codigo="{{ $est['estu_codigo'] }}"
        onclick="verDetalleEstudiante(this.dataset.codigo)"
        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
        <i class="fas fa-search mr-1"></i> Ver
      </button>
      <button data-ppes-id="{{ $est['ppes_id'] ?? '' }}"
        onclick="quitarEstudianteDelProceso(this.dataset.ppesId)"
        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
        <i class="fas fa-trash-alt mr-1"></i> Quitar
      </button>
    </div>
  </td>
</tr>
