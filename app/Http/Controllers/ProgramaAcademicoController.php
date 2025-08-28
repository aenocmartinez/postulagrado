<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\usecase\notificaciones\ListarNotificacionesPorUsuarioUseCase;
use Src\usecase\procesos\BuscarProcesoUseCase;
use Src\usecase\procesos\ListarProcesosUseCase;
use Src\usecase\programas\AsociarCandidatosAProcesoGradoUseCase;
use Src\usecase\programas\BuscarEstudiantesCandidatosGradoUseCase;
use Src\usecase\programas\BuscarEstudianteUseCase;
use Src\usecase\programas\QuitarEstudianteDeProcesoUseCase;
use Src\shared\di\FabricaDeRepositorios;
use Src\usecase\programas\EnviarEnlaceActualizacionUseCase;
use Src\usecase\programas\ObtenerDetalleEstudianteProcesoUseCase;

class ProgramaAcademicoController extends Controller
{
    
    public function dashboard()
    {               
        return view('programa_academico.dashboard.index');
    }

    public function procesos()
    {
        // $listaProcesos = new ListarProcesosUseCase(
        //     FabricaDeRepositorios::getInstance()->getProcesoRepository()
        // );

        // $response = $listaProcesos->ejecutar();

        // return view('programa_academico.procesos.index', [
        //     'procesos' => $response->getData(),
        // ]);
    }

    public function seguimientoProceso($procesoID)
    {

        $buscarProceso = new BuscarProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $listaNotificaciones = new ListarNotificacionesPorUsuarioUseCase(
            FabricaDeRepositorios::getInstance()->getNotificacionRepository()
        );

        $responseBuscarProceso = $buscarProceso->ejecutar($procesoID);
        if ($responseBuscarProceso->getCode() != 200) {
            return redirect()->route('programa_academico.procesos.index')
                                ->with($responseBuscarProceso->getCode(), $responseBuscarProceso->getMessage());
        }

        $listaNotificacionesResponse = $listaNotificaciones->ejecutar(Auth::user()->email);
        if ($listaNotificacionesResponse->getCode() != 200) {
            return redirect()->route('programa_academico.procesos.index')
                                ->with($listaNotificacionesResponse->getCode(), $listaNotificacionesResponse->getMessage());
        }

        /** @var \Src\admisiones\domain\Proceso $proceso */
        $proceso = $responseBuscarProceso->getData();
        

        return view('programa_academico.procesos.seguimiento', [
            'proceso' => $proceso,
            'notificaciones' => $listaNotificacionesResponse->getData(),        
        ]);
    }    

    public function buscarEstudiantesCandidatosAGrado(int $codigoPrograma, int $anio, int $periodo)
    {
    
        // $buscarCantidatosGrado = new BuscarEstudiantesCandidatosGradoUseCase(
        //     FabricaDeRepositorios::getInstance()->getProgramaRepository(),
        // );

        // /** @var App\Models\User $user */

        // $response = $buscarCantidatosGrado->ejecutar(
        //     $user->programaAcademico()->getCodigo(), 
        //     $anio, 
        //     $periodo);


        // return response()->json([
        //         'code' => $response->getCode(),
        //         'message' => $response->getMessage(),
        //         'data' => $response->getData()
        //     ], $response->getCode());

    }

    public function asociarEstudiantesCandidatosAProcesoGrado(Request $request)
    {
        $validated = $request->validate([
            'estudiantes'   => ['required', 'array', 'min:1'],
            'estudiantes.*' => ['required', 'string', 'max:20'],
            'proc_id'      => ['required', 'integer'],
            'anio'      => ['required', 'integer'],
            'periodo'      => ['required', 'integer'],
        ]);

        $estudiantes   = $validated['estudiantes'];
        $procesoID     = $validated['proc_id'];
        $anio          = $validated['anio'];
        $periodo       = $validated['periodo'];


        $asociarCandidatos = new AsociarCandidatosAProcesoGradoUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaRepository(),
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
        );


        $response = $asociarCandidatos->ejecutar($procesoID, $estudiantes, $anio, $periodo);
        
        return response()->json([
            'code' => $response->getCode(),
            'message' => $response->getMessage(),
            'data' => $response->getData()
        ], $response->getCode());
    }

    public function quitarEstudiante(int $estudianteProcesoProgramaID)
    {   
        // $quitarEstudiante = new QuitarEstudianteDeProcesoUseCase(
        //     FabricaDeRepositorios::getInstance()->getProgramaRepository(),
        //     FabricaDeRepositorios::getInstance()->getProcesoRepository(),                
        // );

        // $response = $quitarEstudiante->ejecutar($estudianteProcesoProgramaID);

        // return response()->json([
        //     'code' => $response->getCode(),
        //     'message' => $response->getMessage(),
        //     'data' => $response->getData()
        // ], $response->getCode());
        
    }  
    
    public function buscarEstudiante(Request $request)
    {
        $documentoOrCodigo = $request->get('termino');

        $buscarEstudiante = new BuscarEstudianteUseCase(
            FabricaDeRepositorios::getInstance()->getEstudianteRepository(),
        );
        
        /** @var \Src\shared\response\ResponsePostulaGrado $response */
        $response = $buscarEstudiante->ejecutar($documentoOrCodigo);
        $estudiantes = $response->getData();
                

        return response()->json($estudiantes);
    }

    public function agregarUnEstudianteAProceso(Request $request)
    {
        [$code, $message] = $this->asociarCandidato(
            (int)$request->proceso_id,
            (string)$request->codigo,
            2024,
            1
        );

        $payload = ['code' => $code, 'message' => $message];

        if ($code === 200) {
            $est     = $this->construirEstParaFila((int)$request->proceso_id, (string)$request->codigo);
            $payload['row_html'] = view('programa_academico.procesos.partials.fila_estudiante_vinculado', compact('est'))->render();
        }

        return response()->json($payload, $code);
    }

    private function asociarCandidato(int $procesoId, string $codigo, int $anio, int $periodo): array
    {
        $uc = new AsociarCandidatosAProcesoGradoUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaRepository(),
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
        );

        $resp = $uc->ejecutar($procesoId, [$codigo], $anio, $periodo);
        return [(int)$resp->getCode(), $resp->getMessage()];
    }

    private function construirEstParaFila(int $procesoId, string $codigo): array
    {        
        /** @var Models\User $user */
        $user = Auth::user();
        $lista = $user->programaAcademico()->listarEstudiantesCandidatos($procesoId);
        $est   = collect($lista)->firstWhere('estu_codigo', $codigo);

        if (!$est || empty($est['detalle'])) {
            $buscarUc = new BuscarEstudianteUseCase(
                FabricaDeRepositorios::getInstance()->getEstudianteRepository(),
            );
            $resp = $buscarUc->ejecutar($codigo);

            if ((int)$resp->getCode() === 200) {
                $det = (array) $resp->getData();
                $est = [
                    'ppes_id'     => $est['ppes_id'] ?? null,
                    'estu_codigo' => $codigo,
                    'detalle'     => (object)[
                        'pensum_estud'    => $det['pensum_estud']    ?? ($det['pensum'] ?? '-'),
                        'documento'       => $det['documento']       ?? '-',
                        'nombres'         => $det['nombres']         ?? ($det['nombre'] ?? '-'),
                        'categoria'       => $det['categoria']       ?? '-',
                        'situacion'       => $det['situacion']       ?? '-',
                        'cred_pendientes' => $det['cred_pendientes'] ?? ($det['numeroCreditosPendientes'] ?? '-'),
                    ],
                ];
            }
        }

        return $est;
    }

    public function enviarEnlaceActualizacionAEstudiantes(Request $request)
    {
        $procesoID = $request->get('proceso_id');

        $enviarEnlace = new EnviarEnlaceActualizacionUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaRepository(),
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getEnlaceActualizacionRepository(),
        );

        $response = $enviarEnlace->ejecutar($procesoID);

        return response()->json([
            'code' => $response->getCode(),
            'message' => $response->getMessage(),
        ], $response->getCode());
    }

    /**
     * GET /programa-academico/estudiantes/{procesoId}/{codigo}
     * Retorna el detalle de un estudiante vinculado a un proceso.
     */
    public function detalleEstudianteProceso(int $procesoId, string $codigo)
    {
        try {
            $useCase = new ObtenerDetalleEstudianteProcesoUseCase(
                FabricaDeRepositorios::getInstance()->getProgramaRepository(),
                FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            );

            $resp = $useCase->ejecutar($procesoId, $codigo);

            return response()->json([
                'code'    => $resp->getCode(),
                'message' => $resp->getMessage(),
                'data'    => $resp->getData(),
            ], $resp->getCode());
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'code'    => 500,
                'message' => 'Error al obtener el detalle del estudiante.',
            ], 500);
        }
    }

}
