<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearProceso;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Src\admisiones\procesos\domain\Proceso;
use Src\admisiones\procesos\usecase\CrearProcesoUseCase;
use Src\admisiones\procesos\usecase\ListarProcesosUseCase;

class ProcesoController extends Controller
{
    public function index(Request $request)
    {
        $procesos = ListarProcesosUseCase::ejecutar();
        
        $procesosCollection = collect($procesos);

        if ($request->has('search') && !empty($request->search)) {
            $procesosCollection = $procesosCollection->filter(function (Proceso $proceso) use ($request) {
                return stripos($proceso->getNombre(), $request->search) !== false;
            });
        }

        $procesosCollection = $procesosCollection->sortByDesc(fn ($proceso) => $proceso->getNombre());

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 7;
        $procesosPaginados = new LengthAwarePaginator(
            $procesosCollection->forPage($currentPage, $perPage)->values(),
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

    public function store(CrearProceso $request)
    {
        $response = CrearProcesoUseCase::ejecutar($request->validated());
        
        return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
    }

    public function edit($id)
    {
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
