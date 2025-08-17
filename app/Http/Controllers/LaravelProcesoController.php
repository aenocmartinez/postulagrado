<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarProceso;
use App\Http\Requests\CrearProceso;
use Src\application\procesos\DTO\CrearProcesoDTO;
use Src\Application\procesos\DTO\EditarProcesoDTO;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\Infrastructure\Controller\nivelEducativo\ListarNivelEducativoController;
use Src\infrastructure\controller\proceso\ActualizarProcesoController;
use Src\infrastructure\controller\proceso\BuscarProcesoPorIDController;
use Src\infrastructure\controller\proceso\CrearProcesoController;
use Src\Infrastructure\Controller\Proceso\EliminarProcesoController;
use Src\Infrastructure\Controller\Proceso\ListarProcesoController;
use Src\shared\di\FabricaDeRepositoriosOracle;

class LaravelProcesoController extends Controller
{    
    private ProcesoRepository $procesoRepository;
    private NivelEducativoRepository $nivelEducativoRepository;
    private ProgramaRepository $programaRepository;

    public function __construct()
    {
        $this->procesoRepository = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
        $this->nivelEducativoRepository = FabricaDeRepositoriosOracle::getInstance()->getNivelEducativoRepository();
        $this->programaRepository = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();
    }

    public function index()
    {        
        $response = (new ListarProcesoController($this->procesoRepository, $this->nivelEducativoRepository))();        
        return view('admisiones.procesos.index', [
            'procesos' => $response->getData()
        ]);
    }

    public function create()
    {   
        $response = (new ListarNivelEducativoController($this->nivelEducativoRepository))();  
        
        return view('admisiones.procesos.create', [
            'listaNivelEduactivo' => $response->getData(),
        ]);        
    }

    public function store(CrearProceso $request)
    {
        $data = $request->validated();

        $crearProcesoDTO = new CrearProcesoDTO(
            $data['nombre'],
            $data['nivelEducativo']
        );

        $response = (new CrearProcesoController(
            $this->procesoRepository,
            $this->nivelEducativoRepository,
            $this->programaRepository
        ))($crearProcesoDTO);

        return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
    }

    public function edit($procesoID)
    {
        $response = (new BuscarProcesoPorIDController($this->procesoRepository, $this->nivelEducativoRepository))($procesoID);                
        if ($response->getCode() != 200) {
            return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
        }

        /** @var EditarProcesoDTO $editarProcesoDTO */
        $editarProcesoDTO = $response->getData();

        return view('admisiones.procesos.edit', [
            'proceso' => $editarProcesoDTO,
            'listaNivelEduactivo' => $editarProcesoDTO->nivelesEducativos, 
        ]);
    }

    public function update(ActualizarProceso $request, $id)
    {
        $data = $request->validated();

        $editarProcesoDTO = new EditarProcesoDTO(
            $id,
            $data['nombre'],
            $data['nivelEducativo'],
        );

        $response = (new ActualizarProcesoController(
            $this->procesoRepository,
            $this->nivelEducativoRepository,
        ))($editarProcesoDTO);

        return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
    }

    public function destroy($procesoID) 
    {
        $response = (new EliminarProcesoController($this->procesoRepository))($procesoID);
        return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
    }
}
