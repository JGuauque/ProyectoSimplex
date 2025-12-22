<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Instructor;
use App\Models\User;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
    // Contar el total de clientes
    $totalClientes = Cliente::count();
    $totalClientes = Cliente::count();
    $totalInstructores = Instructor::count();
    $totalUsuarios = User::count();
    $totalProveedores = Proveedor::count();

    // Pasar el total a la vista
    return view('dashboard', compact('totalClientes', 'totalInstructores', 'totalUsuarios', 'totalProveedores'));
    }
}
