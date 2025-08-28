<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarNotificacion;
use Illuminate\Support\Facades\Auth;
use Src\application\procesos\notificaciones\DTO\NotificacionDTO;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\Infrastructure\Controller\Proceso\ListarProcesoController;
use Src\infrastructure\controller\proceso\notificacion\ActualizarNotificacionController;
use Src\infrastructure\controller\proceso\notificacion\AnularNotificacionController;
use Src\infrastructure\controller\proceso\notificacion\CrearNotificacionController;
use Src\infrastructure\controller\proceso\notificacion\FormularioCrearNotificacionController;
use Src\infrastructure\controller\proceso\notificacion\FormularioEditarNotificacionController;
use Src\infrastructure\controller\proceso\notificacion\MarcarNotificacionComoLeidaController;
use Src\infrastructure\controller\proceso\notificacion\NotifiacionesDeUnProcesoController;
use Src\infrastructure\controller\proceso\notificacion\VerNotificacionController;
use Src\shared\di\FabricaDeRepositoriosOracle;


class LaravelNotificacionController extends Controller
{
    private ProcesoRepository $procesoRepo;
    private NivelEducativoRepository $nivelEducativoRepo;
    private NotificacionRepository $notificacionRepo;
    private ContactoRepository $contactoRepo;

    public function __construct()
    {
        $this->procesoRepo = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
        $this->nivelEducativoRepo = FabricaDeRepositoriosOracle::getInstance()->getNivelEducativoRepository();
        $this->notificacionRepo = FabricaDeRepositoriosOracle::getInstance()->getNotificacionRepository();
        $this->contactoRepo = FabricaDeRepositoriosOracle::getInstance()->getContactoRepository();
    }


    public function index()
    {
        $response = (new ListarProcesoController($this->procesoRepo, $this->nivelEducativoRepo))->__invoke();

        return view('admisiones.notificaciones.index', [
            'procesos' => $response->getData(),
        ]);
    }

    public function notificacionesDeUnProceso($procesoID)
    {
        $procesoNotificacion = (new NotifiacionesDeUnProcesoController($this->procesoRepo, $this->notificacionRepo))->__invoke($procesoID);

        return view('admisiones.notificaciones.index_por_proceso', [
            'procesoNotificacion' => $procesoNotificacion->getData(),
        ]);
    }
    
    public function create($procesoID)
    {
        $response = (new FormularioCrearNotificacionController($this->procesoRepo, $this->notificacionRepo, $this->contactoRepo))
            ->__invoke($procesoID);

        if ($response->getCode() !== 200) {
            return redirect()->back()->with('error', $response->getMessage());
        }

        return view('admisiones.notificaciones.create', [
            'procesoNotificacion' => $response->getData(),
        ]);
    }

    public function store(GuardarNotificacion $request)
    {
        $datosValidados = $request->validated();

        $notificacionDTO = new NotificacionDTO();
        $notificacionDTO->procesoID     = $datosValidados['proceso_id'];
        $notificacionDTO->asunto        = $datosValidados['asunto'];
        $notificacionDTO->mensaje       = $datosValidados['mensaje'];
        $notificacionDTO->destinatarios = implode(',', $datosValidados['destinatarios']);
        $notificacionDTO->fechaEnvio    = $datosValidados['fecha_envio'];
        $notificacionDTO->canal         = $datosValidados['canal'];

        $response = (new CrearNotificacionController($this->notificacionRepo, $this->procesoRepo, $this->contactoRepo))
            ->__invoke($notificacionDTO);


        if ($response->getCode() !== 201) {
            return redirect()->back()->with('error', $response->getMessage());
        }
    
        return redirect()->route('notificaciones.por_proceso', ['id' => $datosValidados['proceso_id']])
                        ->with($response->getCode(), $response->getMessage());        
    }   

    public function show($notificacionID)
    {
        $response = (new VerNotificacionController($this->notificacionRepo))->__invoke($notificacionID);        
        if ($response->getCode() !== 200) {
            return redirect()->route('notificaciones.index')->with($response->getCode(), $response->getMessage());
        }
    
        return view('admisiones.notificaciones.show', [
            'notificacion' => $response->getData(),
        ]);
    }

    public function edit($id)
    {

        $response = (new FormularioEditarNotificacionController($this->notificacionRepo, $this->contactoRepo))->__invoke($id);
        
        if ($response->getCode() !== 200) {
            return redirect()->route('notificaciones.index')->with($response->getCode(), $response->getMessage());
        }
    
        return view('admisiones.notificaciones.edit', [
            'notificacion' => $response->getData(),
        ]);
    }  
    
    public function update(GuardarNotificacion $request, $id)
    {
        $datosValidados = $request->validated();

        $notificacionDTO = new NotificacionDTO();
        $notificacionDTO->id            = $id;
        $notificacionDTO->procesoID     = $datosValidados['proceso_id'];
        $notificacionDTO->asunto        = $datosValidados['asunto'];
        $notificacionDTO->mensaje       = $datosValidados['mensaje'];
        $notificacionDTO->destinatarios = implode(',', $datosValidados['destinatarios']);
        $notificacionDTO->fechaEnvio    = $datosValidados['fecha_envio'];
        $notificacionDTO->canal         = $datosValidados['canal'];

        $response = (new ActualizarNotificacionController($this->notificacionRepo, $this->procesoRepo))->__invoke($notificacionDTO);

        if ($response->getCode() !== 200) {
            return redirect()->back()->with($response->getCode(), $response->getMessage());
        }

        return redirect()->route('notificaciones.por_proceso', ['id' => $notificacionDTO->procesoID])
                        ->with($response->getCode(), $response->getMessage());
    }    
    
    public function anular($notificacionID)
    {
        $response = (new AnularNotificacionController($this->notificacionRepo))->__invoke($notificacionID);
        if ($response->getCode() !== 200) 
        {
            return redirect()->route('notificaciones.index')->with($response->getCode(), $response->getMessage());
        }  
        
        $data = $response->getData();

        return redirect()->route('notificaciones.por_proceso', ['id' => $data['procesoID']])
                        ->with($response->getCode(), $response->getMessage());        
    }    

    public function marcarComoLeida($id)
    {
        /** @var App\Models\User $user */
        $user = Auth::user();
        $email = $user->email;

        (new MarcarNotificacionComoLeidaController(
            $this->notificacionRepo,
        ))->__invoke($id, $email);

        return response()->json(['success' => true]);
    }
}
