<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarNotificacion;
use Illuminate\Support\Facades\Auth;
use Src\dto\notificacion\NotificacionDTO;
use Src\usecase\notificaciones\ActualizarNotificacionUseCase;
use Src\usecase\notificaciones\AnularNotificacionUseCase;
use Src\usecase\notificaciones\BuscarNotificacionUseCase;
use Src\usecase\notificaciones\CrearNotificacionUseCase;
use Src\usecase\notificaciones\MarcarNotificacionComoLeidaUseCase;
use Src\usecase\procesos\BuscarProcesoUseCase;
use Src\usecase\procesos\ListarProcesosUseCase;
use Src\usecase\programaContacto\ListarContactosUseCase;
use Src\shared\di\FabricaDeRepositorios;

class NotificacionController extends Controller
{
    
    public function index()
    {
        $listarProcesos = new ListarProcesosUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
        );

        $response = $listarProcesos->ejecutar();
    
        return view('notificaciones.index', [
            'procesos' => $response->getData(),
        ]);
    }

    public function indexPorProceso($procesoID)
    {
        $buscarProceso = new BuscarProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );
    
        $response = $buscarProceso->ejecutar($procesoID);

        /** @var \Src\admisiones\domain\Proceso $proceso */
        $proceso = $response->getData();
        
        if (!$proceso->existe()) {
            return redirect()->route('notificaciones.index')->with($response->getCode(), $response->getMessage());
        }
    
        return view('notificaciones.index_por_proceso', [
            'proceso' => $proceso,
        ]);
    }    

    public function create($procesoID)
    {
        $buscarProceso = new BuscarProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $buscarProceso->ejecutar($procesoID);

        /** @var \Src\admisiones\domain\Proceso $proceso */
        $proceso = $response->getData();
        if (!$proceso->existe()) {
            return redirect()->route('notificaciones.index')->with($response->getCode(), $response->getMessage());
        }


        $listarContactos = new ListarContactosUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
        );
        $response = $listarContactos->ejecutar();
        $contactos = $response->getData();

        return view('notificaciones.create', [
            'contactos' => $contactos,
            'proceso' => $proceso,
        ]);
    }
    
    public function store(GuardarNotificacion $request)
    {
        $crearNotificacion = new CrearNotificacionUseCase(
            FabricaDeRepositorios::getInstance()->getNotificacionRepository(),
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
        );
    
        $notificacionDTO = new NotificacionDTO();
        $notificacionDTO->setAsunto($request->input('asunto'));
        $notificacionDTO->setMensaje($request->input('mensaje'));
        $notificacionDTO->setFechaCreacion($request->input('fecha_envio'));
        $notificacionDTO->setCanal($request->input('canal'));
        $notificacionDTO->setDestinatarios(implode(',', $request->input('destinatarios')));
        $notificacionDTO->setProcesoId($request->input('proceso_id'));
    
        $response = $crearNotificacion->ejecutar($notificacionDTO);
    
        if ($response->getCode() !== 201) {
            return redirect()->back()->with('error', $response->getMessage());
        }
    
        return redirect()->route('notificaciones.por_proceso', ['id' => $notificacionDTO->getProcesoId()])
                        ->with($response->getCode(), $response->getMessage());
    }

    public function show($id)
    {
        $buscarNotificacion = new BuscarNotificacionUseCase(
            FabricaDeRepositorios::getInstance()->getNotificacionRepository()
        );
        
        $response = $buscarNotificacion->ejecutar($id);
        $notificacion = $response->getData();

        if ($response->getCode() !== 200) {
            return redirect()->route('notificaciones.index')->with($response->getCode(), $response->getMessage());
        }
    
        return view('notificaciones.show', [
            'notificacion' => $notificacion,
        ]);
    }

    public function anular($id)
    {
        
        $anularNotificacion = new AnularNotificacionUseCase(
            FabricaDeRepositorios::getInstance()->getNotificacionRepository()
        );
    
        $response = $anularNotificacion->ejecutar((int) $id);
    
        if ($response->getCode() !== 200) 
        {
            return redirect()->route('notificaciones.index')->with($response->getCode(), $response->getMessage());
        }


        /** @var \Src\admisiones\domain\Notificacion $notificacion */
        $notificacion = $response->getData();

        return redirect()->route('notificaciones.por_proceso', ['id' => $notificacion->getProceso()->getId()])
                        ->with($response->getCode(), $response->getMessage());
    }
    
    public function edit($id)
    {
        $buscarNotificacion = new BuscarNotificacionUseCase(
            FabricaDeRepositorios::getInstance()->getNotificacionRepository()
        );
        
        $response = $buscarNotificacion->ejecutar($id);
        if ($response->getCode() !== 200) {
            return redirect()->route('notificaciones.index')->with($response->getCode(), $response->getMessage());
        }

        /** @var \Src\admisiones\domain\Notificacion $notificacion */
        $notificacion = $response->getData();

        $listarContactos = new ListarContactosUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
        );
        $response = $listarContactos->ejecutar();
        $contactos = $response->getData();        
    
        return view('notificaciones.edit', [
            'notificacion' => $notificacion,
            'contactos' => $contactos,
        ]);
    }

    public function update(GuardarNotificacion $request, $id)
    {
        $actualizarNotificacion = new ActualizarNotificacionUseCase(
            FabricaDeRepositorios::getInstance()->getNotificacionRepository(),
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
        );

        $notificacionDTO = new NotificacionDTO();
        $notificacionDTO->setId($id);
        $notificacionDTO->setAsunto($request->input('asunto'));
        $notificacionDTO->setMensaje($request->input('mensaje'));
        $notificacionDTO->setFechaCreacion($request->input('fecha_envio'));
        $notificacionDTO->setCanal($request->input('canal'));
        $notificacionDTO->setDestinatarios(implode(',', $request->input('destinatarios')));
        $notificacionDTO->setProcesoId($request->input('proceso_id'));

        $response = $actualizarNotificacion->ejecutar($notificacionDTO);

        if ($response->getCode() !== 200) {
            return redirect()->back()->with($response->getCode(), $response->getMessage());
        }

        return redirect()->route('notificaciones.por_proceso', ['id' => $notificacionDTO->getProcesoId()])
                        ->with($response->getCode(), $response->getMessage());
    }

    public function marcarComoLeida($id)
    {
        $email = Auth::user()->email;

        $marcarNotificacion = new MarcarNotificacionComoLeidaUseCase(
            FabricaDeRepositorios::getInstance()->getNotificacionRepository(),
        );


        $marcarNotificacion->ejecutar($id, $email);

        return response()->json(['success' => true]);
    }

}
