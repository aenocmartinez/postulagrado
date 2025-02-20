<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProcesoController extends Controller
{
    private $procesos = [
        ['id' => 1, 'nombre' => 'Proceso de grado 2025-1', 'nivelEducativo' => 'Postgrado', 'estado' => 'abierto'],
        ['id' => 2, 'nombre' => 'Proceso de grado 2024-2', 'nivelEducativo' => 'Pregrado', 'estado' => 'cerrado'],
        ['id' => 3, 'nombre' => 'Proceso de grado 2024-1', 'nivelEducativo' => 'Pregrado', 'estado' => 'cerrado'],
        ['id' => 4, 'nombre' => 'Proceso de grado 2023-2', 'nivelEducativo' => 'Pregrado', 'estado' => 'cerrado'],
        ['id' => 5, 'nombre' => 'Proceso de grado 2023-1', 'nivelEducativo' => 'Pregrado', 'estado' => 'cerrado'],
        ['id' => 6, 'nombre' => 'Proceso de grado 2022-2', 'nivelEducativo' => 'Pregrado', 'estado' => 'cerrado'],   
        ['id' => 7, 'nombre' => 'Proceso de grado 2022-1', 'nivelEducativo' => 'Pregrado', 'estado' => 'cerrado'],
        ['id' => 8, 'nombre' => 'Proceso de grado 2021-2', 'nivelEducativo' => 'Pregrado', 'estado' => 'cerrado'],
        ['id' => 9, 'nombre' => 'Proceso de grado 2021-1', 'nivelEducativo' => 'Pregrado', 'estado' => 'cerrado'],
        ['id' => 10, 'nombre' => 'Proceso de grado 2020-2', 'nivelEducativo' => 'Pregrado', 'estado' => 'cerrado'],           
    ];

    public function index(Request $request)
    {

        $procesosCollection = collect($this->procesos)->map(function ($proceso) {
            return (object) $proceso; // Convertir cada array en un objeto
        });
         
        if ($request->has('search') && !empty($request->search)) {
            $procesosCollection = $procesosCollection->filter(function ($proceso) use ($request) {
                return stripos($proceso->nombre, $request->search) !== false;
            });
        }
    
        $procesosCollection = $procesosCollection->sortByDesc('nombre');
    
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 7; 
        $procesosPaginados = new LengthAwarePaginator(
            $procesosCollection->forPage($currentPage, $perPage),
            $procesosCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    
        return view('procesos.index', ['procesos' => $procesosPaginados]);
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
