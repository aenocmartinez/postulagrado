<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\usecase\procesoDocumentos\AdjuntarDocumentoAProcesoUseCase;
use Src\usecase\procesoDocumentos\ListarDocumentosDeProcesoUseCase;
use Src\usecase\procesoDocumentos\ConsultarDocumentoDeProcesoUseCase;
use Src\usecase\procesoDocumentos\EliminarDocumentoDeProcesoUseCase;
use Src\dto\procesoDocumento\ProcesoDocumentoDTO;
use Src\shared\di\FabricaDeRepositorios;
use Src\dto\proceso\ProcesoDTO;

class ProcesoDocumentoController extends Controller
{
    public function index($procesoID)
    {
        $listarDocumentos = new ListarDocumentosDeProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $listarDocumentos->ejecutar($procesoID);

        if ($response->getCode() !== 200) {
            return redirect()->back()->withErrors($response->getMessage());
        }

        $proceso = $response->getData();

        return view('admisiones.proceso_documentos.index', [
            'proceso' => $proceso,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'proceso_id' => 'required|integer',
            'nombre'     => 'required|string|max:255',
            'archivo'    => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:5120', // 5MB máximo
        ]);

        $ruta = $request->file('archivo')->store('documentos_proceso', 'public');

        $procesoDto = new ProcesoDTO("");
        $procesoDto->setId($request->input('proceso_id'));

        $documentoDTO = new ProcesoDocumentoDTO();
        $documentoDTO->setProceso($procesoDto);
        $documentoDTO->setNombre($request->input('nombre'));
        $documentoDTO->setRuta('storage/' . $ruta);

        $adjuntarDocumento = new AdjuntarDocumentoAProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoDocumentoRepository(),
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $adjuntarDocumento->ejecutar($documentoDTO);

        return redirect()->route('proceso_documentos.index', $procesoDto->getId())->with($response->getCode(), $response->getMessage());
    }

    public function create($procesoID)
    {
        $procesoRepo = FabricaDeRepositorios::getInstance()->getProcesoRepository();
        
        $proceso = $procesoRepo->buscarProcesoPorId($procesoID);
    
        if (!$proceso->existe()) {
            return redirect()->route('proceso_documentos.index', $procesoID)
                ->withErrors('El proceso no fue encontrado.');
        }
    
        return view('admisiones.proceso_documentos.create', [
            'proceso' => $proceso,
        ]);
    }    

    public function show($documentoID)
    {
        $consultarDocumento = new ConsultarDocumentoDeProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoDocumentoRepository(),
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
        );

        $response = $consultarDocumento->ejecutar($documentoID);

        if ($response->getCode() !== 200) {
            return redirect()->back()->withErrors($response->getMessage());
        }

        /** @var Src\domain\ProcesoDocumento $documento */
        $documento = $response->getData();

        return response()->download(public_path($documento->getRuta()));
    }

    public function destroy($procesoID, $documentoID)
    {
        $eliminarDocumento = new EliminarDocumentoDeProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoDocumentoRepository()
        );

        $response = $eliminarDocumento->ejecutar($documentoID);

        return redirect()->route('proceso_documentos.index', $procesoID)->with($response->getCode(), $response->getMessage());
    }
}
