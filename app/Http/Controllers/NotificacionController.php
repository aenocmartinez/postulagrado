<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use Illuminate\Http\Request;
use Src\admisiones\usecase\notificaciones\ListarNotificacionesUseCase;
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
    

    public function store(Request $request)
    {
        // Lógica para crear una nueva notificación
        return response()->json(['message' => 'Creando notificación']);
    }

    public function show($id)
    {
        // Lógica para mostrar una notificación específica
        return response()->json(['message' => 'Mostrando notificación', 'id' => $id]);
    }

}
