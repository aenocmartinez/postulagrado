<?php

namespace App\Http\Controllers;


use App\Http\Requests\ActualizarProceso;
use App\Http\Requests\CrearProceso;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Src\domain\Proceso;
use Src\dto\proceso\ProcesoDTO;
use Src\usecase\general\ListarNivelEducativoUseCase;
use Src\usecase\procesos\ActualizarProcesoUseCase;
use Src\usecase\procesos\BuscarProgramaPorProcesoUseCase;
use Src\usecase\procesos\CrearProcesoUseCase;
use Src\usecase\procesos\EditarProcesoUseCase;
use Src\usecase\procesos\EliminarProcesoUseCase;
use Src\usecase\procesos\ListarProcesosUseCase;
use Src\usecase\procesos\QuitarProgramaAProcesoUseCase;
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
        $listarNivelEducativo = new ListarNivelEducativoUseCase(
            FabricaDeRepositorios::getInstance()->getNivelEducativoRepository()
        );

        $response = $listarNivelEducativo->ejecutar();        
        return view('procesos.create', [
            'listaNivelEduactivo' => $response->getData(),
        ]);
    }

    
    public function store(CrearProceso $request)
    {
        $validatedData = $request->validated();

        $crearProceso = new CrearProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getProgramaRepository(),
            FabricaDeRepositorios::getInstance()->getNivelEducativoRepository(),
        );

        $procesoDTO  = new ProcesoDTO($validatedData['nombre']);
        $procesoDTO->setNivelEducativo($validatedData['nivelEducativo']);

        $response = $crearProceso->ejecutar($procesoDTO);
        
        return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
    }

    public function edit($id)
    {
        $editarProceso = new EditarProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );      

        $listarNivelEducativo = new ListarNivelEducativoUseCase(
            FabricaDeRepositorios::getInstance()->getNivelEducativoRepository()
        );

        $response = $editarProceso->ejecutar($id);
        if ($response->getCode() != 200) {
            return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
        }
        
        $nivelesEducativo = $listarNivelEducativo->ejecutar();

        return view('procesos.edit', [
            'proceso' => $response->getData(),
            'listaNivelEduactivo' => $nivelesEducativo->getData(),
        ]);
    }


    public function update(ActualizarProceso $request, $id)
    {
        $actualizarProceso = new ActualizarProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getNivelEducativoRepository(),
        );


        $procesoDTO  = new ProcesoDTO($request['nombre']);
        $procesoDTO->setNivelEducativo($request['nivelEducativo']);

        $response = $actualizarProceso->ejecutar($id, $procesoDTO);
        
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
