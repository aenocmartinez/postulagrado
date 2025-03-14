<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarProceso;
use App\Http\Requests\CrearProceso;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Src\admisiones\domain\Proceso;
use Src\admisiones\usecase\procesos\ActualizarProcesoUseCase;
use Src\admisiones\usecase\procesos\BuscarProgramaPorProcesoUseCase;
use Src\admisiones\usecase\procesos\CrearProcesoUseCase;
use Src\admisiones\usecase\procesos\EditarProcesoUseCase;
use Src\admisiones\usecase\procesos\EliminarProcesoUseCase;
use Src\admisiones\usecase\procesos\ListarProcesosUseCase;
use Src\admisiones\usecase\procesos\QuitarProgramaAProcesoUseCase;
use Src\shared\di\FabricaDeRepositorios;

class ProcesoController extends Controller
{
    public function index(Request $request)
    {
        $listarProcesos = new ListarProcesosUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );
        
        $response = $listarProcesos->ejecutar();
        
        $procesosCollection = collect($response->getData());

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
        $crearProceso = new CrearProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getProgramaRepository()
        );

        $response = $crearProceso->ejecutar($request->validated());
        
        return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
    }

    public function edit($id)
    {
        $editarProceso = new EditarProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $editarProceso->ejecutar($id);
        if ($response->getCode() != 200) {
            return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
        }

        return view('procesos.edit', [
            'proceso' => $response->getData()
        ]);
    }


    public function update(ActualizarProceso $request, $id)
    {
        $actualizarProceso = new ActualizarProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $actualizarProceso->ejecutar($id, $request->validated());
        
        return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
    }


    public function destroy($id)
    {
        $eliminarProceso = new EliminarProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );
        
        $response = $eliminarProceso->ejecutar($id);

        return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
    }

    public function quitarPrograma($procesoID, $programaID)
    {
        $quitarPrograma = new QuitarProgramaAProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getProgramaRepository(),
        );

        $quitarPrograma->ejecutar($procesoID, $programaID);
    }

    public function consultarAvancePrograma($procesoID, $programaID) {

        $buscarProgramaProceso = new BuscarProgramaPorProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getProgramaRepository(),
        );

        $response = $buscarProgramaProceso->ejecutar($procesoID, $programaID);

        return view('seguimientos.programa-avance', [
            'programaProceso' => $response->getData()
        ]);
    }
    
}
