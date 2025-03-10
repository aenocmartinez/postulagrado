<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearContacto;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Src\admisiones\dto\ProgramaContactoDTO;
use Src\admisiones\usecase\programaContacto\ActualizarContactoUseCase;
use Src\admisiones\usecase\programaContacto\CrearContactoUseCase;
use Src\admisiones\usecase\programaContacto\EditarContactoUseCase;
use Src\admisiones\usecase\programaContacto\EliminarContactoUseCase;
use Src\admisiones\usecase\programaContacto\ListarContactosUseCase;
use Src\admisiones\usecase\programaContacto\VerContactoUseCase;
use Src\admisiones\usecase\programas\ListarProgramasUseCase;
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

    public function create() 
    {
        $listarProgramas = new ListarProgramasUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaRepository()
        );

        $response = $listarProgramas->ejecutar();

        return view("contactos.create", [
            'programas' => $response->getData()
        ]);
    }

    public function store(CrearContacto $req) {

        $datosValidados = $req->validated();

        $crearContacto = new CrearContactoUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
        );

        $response = $crearContacto->ejecutar(new ProgramaContactoDTO(
            $datosValidados['nombre'],
            $datosValidados['telefono'],
            $datosValidados['email'],
            (int)$datosValidados['programa_id'],
            $datosValidados['observacion'],
        ));
        
        return redirect()->route('contactos.index')->with($response->getCode(), $response->getMessage());
    }

    public function destroy($id) 
    {

        $eliminarContacto = new EliminarContactoUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
        );

        $response = $eliminarContacto->ejecutar($id);

        return redirect()->route('contactos.index')->with($response->getCode(), $response->getMessage());
    }

    public function show($id) 
    {
        $verContacto = new VerContactoUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository(),
        );

        $response = $verContacto->ejecutar($id);

        if ($response->getCode() != 200) {
            return redirect()->route('contactos.index')->with($response->getCode(), $response->getMessage());  
        }

        return view('contactos.show', [
            'contacto' => $response->getData()
        ]);
    }

    public function edit($id) 
    {
        $editarContacto = new EditarContactoUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository(),
            FabricaDeRepositorios::getInstance()->getProgramaRepository(),
        );

        $response = $editarContacto->ejecutar($id);
        if ($response->getCode() != 200) {
            return redirect()->route('contactos.index')->with($response->getCode(), $response->getMessage());  
        }

        $data = $response->getData();
        
        return view('contactos.edit', [
            'contacto' => $data['contacto'],
            'programas' => $data['programas'],
        ]);
    }    

    public function update(CrearContacto $req, int $id) {
        $datosValidados = $req->validated();
    
        $actualizarContacto = new ActualizarContactoUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
        );
    
        $response = $actualizarContacto->ejecutar($id, new ProgramaContactoDTO(
            $datosValidados['nombre'],
            $datosValidados['telefono'],
            $datosValidados['email'],
            (int)$datosValidados['programa_id'],
            $datosValidados['observacion'],
        ));
    
        return redirect()->route('contactos.index')->with($response->getCode(), $response->getMessage());
    }
    
}
