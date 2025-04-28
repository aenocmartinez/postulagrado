<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use Illuminate\Http\Request;
use Src\admisiones\usecase\notificaciones\BuscarNotificacionUseCase;
use Src\admisiones\usecase\notificaciones\ListarNotificacionesUseCase;
use Src\admisiones\usecase\programaContacto\ListarContactosUseCase;
use Src\shared\di\FabricaDeRepositorios;

class NotificacionController extends Controller
{
    
    public function index()
    {
        $listarNotificaciones = new ListarNotificacionesUseCase(
            FabricaDeRepositorios::getInstance()->getNotifacionRepository()
        );
    
        $response = $listarNotificaciones->ejecutar();
        $notificacionesArray = $response->getData();
    
        // Paginamos manualmente
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Número de registros por página (puedes ajustar)
        $collection = collect($notificacionesArray);
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator(
            $currentPageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );
    
        return view('notificaciones.index', [
            'notificaciones' => $paginated,
        ]);
    }

    public function create()
    {
        $listarContactos = new ListarContactosUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
        );
        $response = $listarContactos->ejecutar();
        $contactos = $response->getData();


        return view('notificaciones.create', [
            'contactos' => $contactos,
        ]);
    }
    
    public function store(Request $request)
    {
        // Lógica para crear una nueva notificación
        return response()->json(['message' => 'Creando notificación']);
    }

    public function show($id)
    {
        
        $buscarNotificacion = new BuscarNotificacionUseCase(
            FabricaDeRepositorios::getInstance()->getNotifacionRepository()
        );
        
        $response = $buscarNotificacion->ejecutar($id);
        $notificacion = $response->getData();

        if ($response->getCode() !== 200) {
            return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
        }
    
        return view('notificaciones.show', [
            'notificacion' => $notificacion,
        ]);
    }

}
