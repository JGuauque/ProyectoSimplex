<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\LocalAliadoController;
use App\Http\Middleware\VerificarTurnoActivo;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\PdfController;

use App\Http\Controllers\EmailController;

use App\Http\Controllers\ContactoController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', 'verified')->group(function () {

    // Route::get('/dashboard', function () {
    //     // Verificar permiso
    //     if (!auth()->user()->can('ver dashboard')) {
    //         // Redirigir a una página permitida (como ventas)
    //         return redirect()->route('ventas.create')->with('error', 'No tienes permiso para ver el dashboard.');
    //     }
    //     return view('dashboard');
    // })->name('dashboard');

    // NUEVO: Usa el DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/ventas-categoria', [DashboardController::class, 'getVentasPorCategoria'])
        ->name('dashboard.ventas-categoria');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('usuarios', UserController::class);

    Route::get('/usuarios/{id}/get-data', [UserController::class, 'getData'])->name('usuarios.get-data');



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



    // routes/web.php
    Route::middleware([VerificarTurnoActivo::class])->group(function () {
        // ... otras rutas que requieran turno activo

        Route::get('/ventas/create', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
        Route::get('/buscar-productos', function (Request $request) {

            $query = $request->get('q');

            $productos = \App\Models\Producto::when($query, function ($q) use ($query) {
                return $q->where('nombre', 'like', "%{$query}%")
                    ->orWhere('codigo', 'like', "%{$query}%");
            })
                ->where('stock', '>', 0) // Solo productos con stock disponible
                ->limit(20)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'codigo', 'precio', 'stock']);

            return response()->json($productos);

            // $query = $request->get('q');

            // $productos = \App\Models\Producto::where('nombre', 'LIKE', "%{$query}%")
            //     ->orWhere('codigo', 'LIKE', "%{$query}%")
            //     ->where('stock', '>', 0)
            //     ->select('id', 'nombre', 'codigo', 'precio', 'stock')
            //     ->limit(10)
            //     ->get();

            // return response()->json($productos);
        });
    });





    // Verifica que tengas estas rutas
    Route::prefix('factura')->group(function () {
        Route::get('/pdf/80mm/{venta}', [PdfController::class, 'factura80mm'])
            ->name('factura.pdf.80mm')
            ->whereNumber('venta');

        Route::get('/pdf/a4/{venta}', [PdfController::class, 'facturaA4'])
            ->name('factura.pdf.a4')
            ->whereNumber('venta');
    });

    Route::get('/prestamos/exportar-pdf', [PdfController::class, 'exportarPDF'])
        ->name('prestamos.exportar.pdf');



    // Rutas para envío de comprobantes
    Route::post('/venta/{venta}/enviar-email', [EmailController::class, 'enviarComprobanteEmail'])
        ->name('venta.enviar.email')
        ->where('venta', '[0-9]+'); // Solo números;


    // En routes/web.php
    Route::get('/test-email/{venta_id}', function ($venta_id) {
        $venta = App\Models\Venta::find($venta_id);

        if (!$venta) {
            return "Venta no encontrada";
        }

        try {
            Mail::to('test@example.com')->send(new App\Mail\ComprobanteVentaMail($venta, 'test@example.com'));
            return "Email enviado exitosamente!";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    });







    Route::resource('clientes', ClienteController::class);
    Route::get('/clientes', [ClienteController::class, 'index'])->name('cliente.index');
    Route::get('/clientes/{cliente}/ventas', [ClienteController::class, 'getVentas'])->name('clientes.ventas');
    Route::get('/buscar-clientes', [ClienteController::class, 'buscar'])->name('clientes.buscar');

    // Gestión de préstamos (vista principal)
    Route::get('/prestamos', [PrestamoController::class, 'index'])->name('prestamo.index');
    // Crear préstamo (POST)
    Route::post('/prestamos', [PrestamoController::class, 'store'])->name('prestamos.store');
    // Cambiar estado de préstamo
    Route::put('/prestamos/{prestamo}/estado', [PrestamoController::class, 'updateEstado'])
        ->name('prestamos.estado');
    // Eliminar préstamo
    Route::delete('/prestamos/{prestamo}', [PrestamoController::class, 'destroy'])
        ->name('prestamos.destroy');
    // Locales aliados
    Route::resource('locales-aliados', LocalAliadoController::class);
});

Route::post('/contacto/enviar', [ContactoController::class, 'enviar'])->name('contacto.enviar');

// ========== RUTAS API (Para AJAX/JavaScript) ==========
Route::prefix('api')->group(function () {
    // Obtener préstamos para la lista (AJAX)
    Route::get('/prestamos', function () {
        $prestamos = \App\Models\Prestamo::with(['local', 'producto'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($prestamos);
    });
    // Obtener productos con stock
    Route::get('/productos-con-stock', function () {
        $productos = \App\Models\Producto::where('stock', '>', 0)
            ->select('id', 'nombre', 'stock', 'precio', 'costo')
            ->get();
        return response()->json($productos);
    });
    // Obtener locales aliados
    Route::get('/locales-aliados', function () {
        $locales = \App\Models\LocalAliado::where('activo', true)
            ->select('id', 'nombre', 'identificacion', 'contacto', 'direccion')
            ->get();
        return response()->json($locales);
    });


    // Rutas para locales aliados (API)
    Route::prefix('locales-aliados')->group(function () {
        Route::get('/', [LocalAliadoController::class, 'index'])->name('locales.index');
        Route::post('/', [LocalAliadoController::class, 'store'])->name('locales.store');
        Route::delete('/{local}', [LocalAliadoController::class, 'destroy'])->name('locales.destroy');
    });

    Route::put('/locales-aliados/{id}', [LocalAliadoController::class, 'update'])
        ->name('locales-aliados.update');

    Route::get('/locales-aliados/{id}', [LocalAliadoController::class, 'show'])
        ->name('locales-aliados.show');

    Route::get('/api/productos-con-stock', function () {
        $productos = \App\Models\Producto::where('stock', '>', 0)->get();
        return response()->json($productos);
    });

    // routes/web.php

    Route::prefix('turnos')->group(function () {
        Route::get('/', [TurnoController::class, 'index'])->name('turno.index');
        Route::post('/abrir', [TurnoController::class, 'abrir'])->name('turnos.abrir');
        Route::post('/cerrar', [TurnoController::class, 'cerrar'])->name('turnos.cerrar');
        Route::get('/historial', [TurnoController::class, 'historial'])->name('turnos.historial');
        // routes/web.php
        Route::get('/estado', [TurnoController::class, 'estado'])->name('turnos.estado');
    });
});

require __DIR__ . '/auth.php';
