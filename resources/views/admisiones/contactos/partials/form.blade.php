<!-- Nombre -->
<div class="mb-4">
    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
    <input type="text" name="nombre" id="nombre"
        value="{{ old('nombre', isset($contacto) ? $contacto['nombre'] : '') }}"
        class="w-full px-3 py-2 rounded-md focus:ring focus:ring-gray-400 outline-none
           border {{ $errors->has('nombre') ? 'border-red-500' : 'border-gray-300' }}">
    @error('nombre')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Teléfono -->
<div class="mb-4">
    <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
    <input type="text" name="telefono" id="telefono"
        value="{{ old('telefono', isset($contacto) ? $contacto['telefono'] : '') }}"
        class="w-full px-3 py-2 rounded-md focus:ring focus:ring-gray-400 outline-none
           border {{ $errors->has('telefono') ? 'border-red-500' : 'border-gray-300' }}">
    @error('telefono')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Email -->
<div class="mb-4">
    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
    <input type="email" name="email" id="email"
        value="{{ old('email', isset($contacto) ? $contacto['email'] : '') }}"
        class="w-full px-3 py-2 rounded-md focus:ring focus:ring-gray-400 outline-none
           border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }}">
    @error('email')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Selección de Programa con Búsqueda -->
<div class="mb-4">
    <label for="programa_id" class="block text-sm font-medium text-gray-700">Programa</label>
    <select name="programa_id" id="programa_id"
        class="w-full px-3 py-2 rounded-md focus:ring focus:ring-gray-400 outline-none
            border {{ $errors->has('programa_id') ? 'border-red-500' : 'border-gray-300' }}">
        <option value="">Seleccione un programa</option>
        @foreach ($programas as $programa)
            <option value="{{ $programa['id'] }}"
                {{ old('programa_id', isset($contacto) ? $contacto['programaID'] : '') == $programa['id'] ? 'selected' : '' }}>
                {{ $programa['nombre'] }}
            </option>
        @endforeach
    </select>
    @error('programa_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Observación -->
<div class="mb-4">
    <label for="observacion" class="block text-sm font-medium text-gray-700">Observación</label>
    <textarea name="observacion" id="observacion" rows="3"
        class="w-full px-3 py-2 rounded-md focus:ring focus:ring-gray-400 outline-none
              border {{ $errors->has('observacion') ? 'border-red-500' : 'border-gray-300' }}">{{ old('observacion', isset($contacto) ? $contacto['observacion'] : '') }}</textarea>
    @error('observacion')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
