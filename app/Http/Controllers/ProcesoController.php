<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarProceso;
use App\Http\Requests\CrearProceso;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Src\admisiones\domain\Proceso;
use Src\admisiones\usecase\procesos\ActualizarProcesoUseCase;
use Src\admisiones\usecase\procesos\CrearProcesoUseCase;
use Src\admisiones\usecase\procesos\EditarProcesoUseCase;
use Src\admisiones\usecase\procesos\EliminarProcesoUseCase;
use Src\admisiones\usecase\procesos\ListarProcesosUseCase;

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
        $response = EditarProcesoUseCase::ejecutar($id);
        if ($response->getCode() != 200) {
            return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
        }

        return view('procesos.edit', [
            'proceso' => $response->getData()
        ]);
    }

    public function update(ActualizarProceso $request, $id)
    {
        $response = ActualizarProcesoUseCase::ejecutar($request->validated());
        
        return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
    }

    public function destroy($id)
    {
        $response = EliminarProcesoUseCase::ejecutar($id);
        return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
    }
}
