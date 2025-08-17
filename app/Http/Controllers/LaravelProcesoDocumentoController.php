<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Src\application\procesos\documentos\DTO\GuardarDocumentoDTO;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoDocumentoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\infrastructure\controller\proceso\BuscarProcesoPorIDController;
use Src\infrastructure\controller\proceso\documento\EliminarDocumentoController;
use Src\infrastructure\controller\proceso\documento\GuardarDocumentoController;
use Src\infrastructure\controller\proceso\documento\ListarDocumentosController;
use Src\shared\di\FabricaDeRepositoriosOracle;

class LaravelProcesoDocumentoController extends Controller
{
    private ProcesoDocumentoRepository $documentoRepo;
    private ProcesoRepository $procesoRepo;
    private NivelEducativoRepository $nivelRepo;

    public function __construct() {
        $this->documentoRepo = FabricaDeRepositoriosOracle::getInstance()->getProcesoDocumentoRepository();
        $this->procesoRepo = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
        $this->nivelRepo = FabricaDeRepositoriosOracle::getInstance()->getNivelEducativoRepository();
    }

    public function index($procesoID)
    {
        $response = (new ListarDocumentosController(
            $this->documentoRepo, 
            $this->procesoRepo))($procesoID);
        
        if ($response->getCode() !== 200) {
            return redirect()->back()->withErrors($response->getMessage());
        }

        return view('admisiones.procesos.documentos.index', [
            'procesoDocumento' => $response->getData(),
        ]);
    }

    public function create($procesoID)
    {
        $response = (new BuscarProcesoPorIDController(
                    $this->procesoRepo, 
                    $this->nivelRepo))($procesoID);    

        if ($response->getCode() !== 200) {
            return redirect()->route('proceso_documentos.index', $procesoID)
                ->withErrors('El proceso no fue encontrado.');
        }
    
        return view('admisiones.procesos.documentos.create', [
            'procesoID' => $procesoID,
        ]);
    }   
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'proceso_id' => 'required|integer',
            'nombre'     => 'required|string|max:255',
            'archivo'    => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:5120',
        ]);

        $ruta = $request->file('archivo')->store('documentos_proceso', 'public');

        $documentoDTO = new GuardarDocumentoDTO();
        $documentoDTO->procesoID = $data['proceso_id'];
        $documentoDTO->nombre = $data['nombre'];
        $documentoDTO->ruta = 'storage/' . $ruta;

        $response = (new GuardarDocumentoController($this->documentoRepo, $this->procesoRepo))($documentoDTO);
        if ($response->getCode() !== 201) {
            return redirect()->back()->withErrors($response->getMessage());
        }

        return redirect()->route('procesos.documentos.index', $documentoDTO->procesoID)
                ->with($response->getCode(), $response->getMessage());
    }

    public function destroy($procesoID, $documentoID)
    {

        $response = (new EliminarDocumentoController(
                        $this->documentoRepo,
                        $this->procesoRepo
                    ))($documentoID);

        if ($response->getCode() !== 200) {
            return redirect()->back()->withErrors($response->getMessage());
        }

        return redirect()->route('procesos.documentos.index', $procesoID)
                ->with($response->getCode(), $response->getMessage());
    }    
}
