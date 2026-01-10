<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// RUTAS PARA EL MODULO USUARIOS 

    Route::resource('usuarios', UserController::class);

    Route::get('/usuarios/{id}/get-data', [UserController::class, 'getUsuarioData'])->name('usuarios.get-data');



    // Ruta para resetear contraseña
    Route::post('/usuarios/{usuario}/reset-password', [UserController::class, 'resetPassword'])
        ->name('usuarios.reset-password');






    // Ruta para resetear contraseña por email
    Route::post('/reset-password-email', function (Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'usuario_id' => 'required|exists:users,id'
        ]);

        // Enviar enlace de reset usando Breeze
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Enlace de restablecimiento enviado al email.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo enviar el enlace. Intente nuevamente.'
            ], 500);
        }
    })->middleware('auth');

    Route::post('/cambiar-password-directo', function (Request $request) {
        $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'password' => 'required|min:8|confirmed'
        ]);

        $usuario = \App\Models\User::find($request->usuario_id);
        $usuario->password = Hash::make($request->password);
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    })->middleware('auth');

    Route::resource('inventario', ProductoController::class);
    Route::get('/inventario/{id}/get-data', [ProductoController::class, 'getProductoData'])->name('inventario.get-data');

    Route::resource('clientes', ClienteController::class);
    Route::get('/clientes', [ClienteController::class, 'index'])->name('cliente.index');
    Route::get('/clientes/{cliente}/ventas', [ClienteController::class, 'getVentas'])->name('clientes.ventas');
    Route::get('/buscar-clientes', [ClienteController::class, 'buscar'])->name('clientes.buscar');



    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});

require __DIR__.'/auth.php';
