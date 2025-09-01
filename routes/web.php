<?php

use App\Http\Controllers\LaravelActualizacionDatosController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/actualizar-datos/{token}', [LaravelActualizacionDatosController::class, 'mostrarFormularioActualizacion'])
     ->name('actualizacion.form.token');


Route::post('/postula-grado/actualizacion', [LaravelActualizacionDatosController::class, 'gurdarDatosEstudiante'])
    ->name('postulacion.actualizacion.store');

require __DIR__.'/auth.php';
