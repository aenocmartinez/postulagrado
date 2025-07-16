<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarNotificacion;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use Src\admisiones\dto\notificacion\NotificacionDTO;
use Src\admisiones\usecase\notificaciones\ActualizarNotificacionUseCase;
use Src\admisiones\usecase\notificaciones\AnularNotificacionUseCase;
use Src\admisiones\usecase\notificaciones\BuscarNotificacionUseCase;
use Src\admisiones\usecase\notificaciones\CrearNotificacionUseCase;
use Src\admisiones\usecase\notificaciones\ListarNotificacionesUseCase;
use Src\admisiones\usecase\procesos\BuscarProcesoUseCase;
use Src\admisiones\usecase\procesos\ListarProcesosUseCase;
use Src\admisiones\usecase\programaContacto\ListarContactosUseCase;
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


}
