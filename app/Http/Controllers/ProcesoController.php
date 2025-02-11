<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProcesoController extends Controller
{
    private $procesos = [
        ['id' => 1, 'nombre' => 'Proceso de grado 2025-1', 'estado' => 'abierto'],
        ['id' => 2, 'nombre' => 'Proceso de grado 2024-2', 'estado' => 'cerrado'],
        ['id' => 3, 'nombre' => 'Proceso de grado 2024-1', 'estado' => 'cerrado'],
        ['id' => 4, 'nombre' => 'Proceso de grado 2023-2', 'estado' => 'cerrado'],
        ['id' => 5, 'nombre' => 'Proceso de grado 2023-1', 'estado' => 'cerrado'],
        ['id' => 6, 'nombre' => 'Proceso de grado 2022-2', 'estado' => 'cerrado'],   
        // ['id' => 7, 'nombre' => 'Proceso de grado 2022-1', 'estado' => 'cerrado'],
        // ['id' => 8, 'nombre' => 'Proceso de grado 2021-2', 'estado' => 'cerrado'],
        // ['id' => 9, 'nombre' => 'Proceso de grado 2021-2', 'estado' => 'cerrado'],
        // ['id' => 10, 'nombre' => 'Proceso de grado 2020-2', 'estado' => 'cerrado'],           
    ];

    public function index()
    {
        return view('procesos.index', ['procesos' => $this->procesos]);
    }

    public function create()
    {
        return view('procesos.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('procesos.index')->with('success', 'Proceso creado con éxito.');
    }

    public function edit($id)
    {
        $proceso = collect($this->procesos)->firstWhere('id', $id);

        if (!$proceso) {
            return redirect()->route('procesos.index')->with('error', 'Proceso no encontrado.');
        }

        return view('procesos.edit', compact('proceso'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('procesos.index')->with('success', 'Proceso actualizado con éxito.');
    }

    public function destroy($id)
    {
        return redirect()->route('procesos.index')->with('success', 'Proceso eliminado.');
    }
}
