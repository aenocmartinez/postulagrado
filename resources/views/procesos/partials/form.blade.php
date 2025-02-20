<!-- Nombre -->
<div class="mb-4">
    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Proceso</label>
    <input type="text" name="nombre" id="nombre"
        class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none
        @error('nombre') border-red-500 @enderror"
        value="{{ old('nombre', isset($proceso) ? $proceso->getNombre() : '') }}">
    
    @error('nombre')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Nivel Educativo -->
<div class="mb-4">
    <label for="nivelEducativo" class="block text-sm font-medium text-gray-700 mb-2">Nivel Educativo</label>
    <select name="nivelEducativo" id="nivelEducativo"
        class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none
        @error('nivelEducativo') border-red-500 @enderror">
        
        <option value="Pregrado" {{ old('nivelEducativo', isset($proceso) ? $proceso->getNivelEducativo() : '') == 'Pregrado' ? 'selected' : '' }}>
            Pregrado
        </option>
        <option value="Postgrado" {{ old('nivelEducativo', isset($proceso) ? $proceso->getNivelEducativo() : '') == 'Postgrado' ? 'selected' : '' }}>
            Postgrado
        </option>
    </select>

    @error('nivelEducativo')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado (Solo en edición) -->
@if(isset($proceso))
<div class="mb-4">
    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
    <select name="estado" id="estado"
        class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none
        @error('estado') border-red-500 @enderror">
        
        <option value="Abierto" {{ old('estado', $proceso->getEstado()) == 'Abierto' ? 'selected' : '' }}>Abierto</option>
        <option value="Cerrado" {{ old('estado', $proceso->getEstado()) == 'Cerrado' ? 'selected' : '' }}>Cerrado</option>
    </select>

    @error('estado')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
@endif
