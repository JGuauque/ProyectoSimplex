<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ActivoController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    //PROVEEDORES
    Route::resource('/proveedores', ProveedorController::class);
    //ACTIVOS
    Route::resource('/activos', ActivoController::class);

    Route::resource('clientes', ClienteController::class);

    Route::resource('instructores', InstructorController::class);
    
    Route::resource('clases', ClaseController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //REPORTES
    Route::get('/pdfClientes', [PdfController::class, 'pdfClientes'])->name('pdf.clientes');   
    Route::get('/pdfInstructores', [PdfController::class, 'pdfInstructores'])->name('pdf.instructores');
    Route::get('/pdfProveedores', [PdfController::class, 'pdfProveedores'])->name('pdf.proveedores');
    Route::get('/pdfActivos', [PdfController::class, 'pdfActivos'])->name('pdf.activos');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/api/grafico-datos', function () {
        return response()->json([
            'clientes' => \App\Models\Cliente::count(),
            'instructores' => \App\Models\Instructor::count(),
            'usuarios' => \App\Models\User::count(),
            'proveedores' => \App\Models\Proveedor::count(),
        ]);
    });
});

require __DIR__.'/auth.php';
