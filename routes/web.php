<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\SismauleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/sismaule', [SismauleController::class, 'index'])->name('sismaule.index');
    Route::match(['get', 'post'], '/sismaule/paciente-grupo-prioritario', [SismauleController::class, 'obtenerPacienteGrupoPrioritario'])
        ->name('sismaule.paciente-grupo-prioritario');
    Route::get('/sismaule/descargar-csv', [SismauleController::class, 'descargarCsv'])
        ->name('sismaule.descargar-csv');
    Route::get('/sismaule/archivos-csv', [SismauleController::class, 'listarArchivosCsv'])
        ->name('sismaule.archivos-csv');

    Route::middleware('admin')->group(function () {
        Route::get('/admin/usuarios', [AdminUserController::class, 'index'])
            ->name('admin.users.index');
        Route::post('/admin/usuarios', [AdminUserController::class, 'store'])
            ->name('admin.users.store');
    });
});
