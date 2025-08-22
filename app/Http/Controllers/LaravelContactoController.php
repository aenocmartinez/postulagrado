<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearContacto;
use Src\application\programas\contactos\ActualizarContactoUseCase;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\infrastructure\controller\programa\contacto\ActualizarContactoController;
use Src\infrastructure\controller\programa\contacto\CrearContactoController;
use Src\infrastructure\controller\programa\contacto\EliminarContactoController;
use Src\infrastructure\controller\programa\contacto\FormularioContactoController;
use Src\infrastructure\controller\programa\contacto\FormularioEditarContactoController;
use Src\infrastructure\controller\programa\contacto\ListarContactosController;
use Src\infrastructure\controller\programa\contacto\VerContactoController;
use Src\shared\di\FabricaDeRepositoriosOracle;

class LaravelContactoController extends Controller
{

    private ContactoRepository $contactoRepo;
    private ProgramaRepository $programaRepo;

    public function __construct()
    {
        $this->contactoRepo = FabricaDeRepositoriosOracle::getInstance()->getContactoRepository();
        $this->programaRepo = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();
    }

    public function index()
    {
        $response = (new ListarContactosController($this->contactoRepo))();
        return view("admisiones.contactos.index", [
            "contactos" => $response->getData(),
        ]);        
    }

    public function create()
    {
        $response = (new FormularioContactoController($this->programaRepo))();
        return view("admisiones.contactos.create", [
            'programas' => $response->getData()
        ]);
    }

    public function store(CrearContacto $req)
    {
        $datosValidados = $req->validated();

        $response = (new CrearContactoController($this->contactoRepo, $this->programaRepo))($datosValidados);

        return redirect()->route('contactos.index')->with($response->getCode(), $response->getMessage());   
    }

    public function show($id) 
    {
        $response = (new VerContactoController($this->contactoRepo))->__invoke($id);

        if ($response->getCode() != 200) {
            return redirect()->route('contactos.index')->with($response->getCode(), $response->getMessage());  
        }

        return view('admisiones.contactos.show', [
            'contacto' => $response->getData()
        ]);
    }
    
    public function edit($id) 
    {
        $response = (new FormularioEditarContactoController($this->contactoRepo, $this->programaRepo))->__invoke($id);

        if ($response->getCode() != 200) {
            return redirect()->route('contactos.index')->with($response->getCode(), $response->getMessage());  
        }

        $data = $response->getData();
        
        return view('admisiones.contactos.edit', [
            'contacto' => $data['contacto'],
            'programas' => $data['programas'],
        ]);
    }  
    
    public function update(CrearContacto $req, int $id) {

        $datosValidados = $req->validated();

        $response = (new ActualizarContactoController($this->contactoRepo, $this->programaRepo))->__invoke($id, $datosValidados);

        return redirect()->route('contactos.index')->with($response->getCode(), $response->getMessage());  
    }    

    public function destroy($id) 
    {
        $response = (new EliminarContactoController($this->contactoRepo))->__invoke($id);

        return redirect()->route('contactos.index')->with($response->getCode(), $response->getMessage());
    }    
}
