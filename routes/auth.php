<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ProcesoController;
use App\Http\Controllers\ProgramaContactoController;
use App\Http\Controllers\SeguimientoController;
use Illuminate\Support\Facades\Route;
use Src\admisiones\dto\ProgramaContactoDTO;

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
    Route::get('procesos', [ProcesoController::class, 'index'])->name('procesos.index');
    Route::get('procesos/create', [ProcesoController::class, 'create'])->name('procesos.create');
    Route::post('procesos', [ProcesoController::class, 'store'])->name('procesos.store');
    Route::get('procesos/{id}/edit', [ProcesoController::class, 'edit'])->name('procesos.edit');
    Route::put('procesos/{id}', [ProcesoController::class, 'update'])->name('procesos.update');
    Route::delete('procesos/{id}', [ProcesoController::class, 'destroy'])->name('procesos.destroy');
    Route::delete('procesos/{procesoID}/programas/{programaID}', [ProcesoController::class, 'quitarPrograma'])->name('procesos.quitar-programa');
    Route::get('procesos/{procesoID}/programas/{programaID}', [ProcesoController::class, 'consultarAvancePrograma'])->name('seguimiento.programa-avance');

    // Actividades
    Route::get('procesos/{id}/actividades', [CalendarioController::class, 'index'])->name('procesos.actividades');
    Route::post('procesos/{id}/actividades', [CalendarioController::class, 'store'])->name('actividades.store');
    Route::delete('procesos/{id}/actividades/{actividad}', [CalendarioController::class, 'destroy'])->name('actividades.destroy');

    // Seguimientos
    Route::get('seguimientos', [SeguimientoController::class, 'index'])->name('seguimientos.index');
    Route::get('procesos/{id}/seguimiento', [SeguimientoController::class, 'show'])->name('seguimientos.show');

    // Contactos
    Route::get('contactos', [ProgramaContactoController::class, 'index'])->name('contactos.index');
    Route::get('contactos/crear', [ProgramaContactoController::class, 'create'])->name('contactos.create');
    Route::get('contactos/{id}/editar', [ProgramaContactoController::class, 'edit'])->name('contactos.edit');
    Route::get('contactos/{id}', [ProgramaContactoController::class, 'show'])->name('contactos.show');
    Route::delete('contactos/{id}', [ProgramaContactoController::class, 'destroy'])->name('contactos.destroy');
    Route::post('contactos', [ProgramaContactoController::class, 'store'])->name('contactos.store');
    Route::put('contactos', [ProgramaContactoController::class, 'store'])->name('contactos.update');
    

});
