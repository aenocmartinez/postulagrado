<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Src\admisiones\usecase\programaContacto\ListarContactosUseCase;
use Src\shared\di\FabricaDeRepositorios;

class ProgramaContactoController extends Controller
{
    public function index()
    {
        $criterio = request('criterio', "");

        $listarContactos = new ListarContactosUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
        );

        $contactosArray = $listarContactos->ejecutar($criterio)->getData();

        $perPage = 10; 
        $page = request('page', 1); 
        $contactosCollection = new Collection($contactosArray);
    
        $contactos = new LengthAwarePaginator(
            $contactosCollection->forPage($page, $perPage), 
            $contactosCollection->count(), 
            $perPage, 
            $page, 
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view("contactos.index", [
            "contactos" => $contactos
        ]);
    }
}
