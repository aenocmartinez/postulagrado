<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\LaravelActividadController;
use App\Http\Controllers\LaravelContactoController;
use App\Http\Controllers\LaravelProcesoController;
use App\Http\Controllers\LaravelProcesoDocumentoController;
use App\Http\Controllers\LaravelSeguimientoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ProcesoController;
use App\Http\Controllers\ProgramaAcademicoController;
use App\Http\Controllers\ProgramaContactoController;
use App\Http\Controllers\SeguimientoController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Route::get('register', [RegisteredUserController::class, 'create'])
    //     ->name('register');

    // Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');


    // Procesos
    Route::controller(LaravelProcesoController::class)->group(function () {
        Route::get('procesos', 'index')->name('procesos.index');
        Route::get('procesos/crear', 'create')->name('procesos.create');
        Route::post('procesos', 'store')->name('procesos.store');
        Route::get('procesos/{id}/edit', 'edit')->name('procesos.edit');
        Route::put('procesos/{id}', 'update')->name('procesos.update');
        Route::delete('procesos/{id}', 'destroy')->name('procesos.destroy');
        Route::delete('procesos/{procesoID}/programas/{programaID}', 'quitarPrograma')->name('procesos.quitar-programa');

    });

    
    Route::get('procesos/{procesoID}/programas/{programaID}', [ProcesoController::class, 'consultarAvancePrograma'])->name('seguimiento.programa-avance');    
    
    // Actividades
    Route::controller(LaravelActividadController::class)->group(function () {
        Route::get('procesos/{id}/actividades', 'index')->name('procesos.actividades');
        Route::post('/procesos/{id}/actividades', 'store')->name('actividades.store');
    });

    // Documentos de Proceso
    Route::controller(LaravelProcesoDocumentoController::class)->group(function () {
        Route::get('/procesos/{procesoID}/documentos', 'index')->name('procesos.documentos.index');
        Route::get('/procesos/{procesoID}/crear-documento', 'create')->name('procesos.documentos.create');
        Route::post('/procesos/documentos', 'store')->name('proceso_documentos.store');
        Route::delete('/proceso/{procesoID}/documentos/{documentoID}', 'destroy')->name('procesos.documentos.destroy');
    });

    // Contactos
    Route::controller(LaravelContactoController::class)->group(function () {
        Route::get('contactos', 'index')->name('contactos.index');
        Route::get('contactos/crear', 'create')->name('contactos.create');
        Route::post('contactos', 'store')->name('contactos.store');
        Route::get('contactos/{id}', 'show')->name('contactos.show');
        Route::get('contactos/{id}/editar', 'edit')->name('contactos.edit');     
        Route::put('contactos/{id}', 'update')->name('contactos.update');    
        Route::delete('contactos/{id}', 'destroy')->name('contactos.destroy');
    }); 

    // Seguimientos
    Route::controller(LaravelSeguimientoController::class)->group(function () {
        Route::get('seguimientos', 'index')->name('seguimientos.index');
        Route::get('procesos/{id}/seguimiento', 'show')->name('seguimientos.show');
    });
    
    
    // Notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('notificaciones/proceso/{id}/crear', [NotificacionController::class, 'create'])->name('notificaciones.create');
    Route::post('/notificaciones', [NotificacionController::class, 'store'])->name('notificaciones.store');
    Route::get('/notificaciones/{id}', [NotificacionController::class, 'show'])->name('notificaciones.show');
    Route::patch('/notificaciones/{id}/anular', [NotificacionController::class, 'anular'])->name('notificaciones.anular');
    Route::get('notificaciones/proceso/{id}', [NotificacionController::class, 'indexPorProceso'])->name('notificaciones.por_proceso');
    Route::get('/notificaciones/{id}/editar', [NotificacionController::class, 'edit'])->name('notificaciones.edit');
    Route::put('/notificaciones/{id}', [NotificacionController::class, 'update'])->name('notificaciones.update');
    Route::post('/notificaciones/{id}/marcar-leida', [NotificacionController::class, 'marcarComoLeida'])->name('notificaciones.marcar_como_leida');
    

    // Programa Académico
    Route::get('/programa_academico/dashboard', [ProgramaAcademicoController::class, 'dashboard'])->name('programa_academico.dashboard');
    Route::get('/programa_academico/procesos', [ProgramaAcademicoController::class, 'procesos'])->name('programa_academico.procesos.index');
    Route::get('/programa_academico/procesos/{id}/seguimiento', [ProgramaAcademicoController::class, 'seguimientoProceso'])->name('programa_academico.procesos.seguimiento');
    Route::get('/programa_academico/estudiantes-candidatos/{codigoPrograma}/{anio}/{periodo}', [ProgramaAcademicoController::class, 'buscarEstudiantesCandidatosAGrado'])->name('programa_academico.candidatos-grado');
    
    Route::post('/programa_academico/asociar-estudiantes', [ProgramaAcademicoController::class, 'asociarEstudiantesCandidatosAProcesoGrado'])->name('programa_academico.asociar-estudiantes-proceso');
    
    Route::get('/programa-academico/estudiantes/{procesoId}/{codigo}',[ProgramaAcademicoController::class, 'detalleEstudianteProceso']
    )->name('programa_academico.estudiantes.detalle');

    Route::post('/programa_academico/enviar-enlace-actualizacion',
        [ProgramaAcademicoController::class, 'enviarEnlaceActualizacionAEstudiantes']
    )->name('programa_academico.enviar-enlace-actualizacion');



    Route::delete('/programa-academico/estudiantes/{estudianteProcesoProgramaID}', [ProgramaAcademicoController::class, 'quitarEstudiante'])
        ->name('programa_academico.estudiantes.quitar');

    Route::get('/proceso-estudiante/buscar', [ProgramaAcademicoController::class, 'buscarEstudiante']);
    Route::post('/proceso-estudiante/agregar', [ProgramaAcademicoController::class, 'agregarUnEstudianteAProceso']);

});
