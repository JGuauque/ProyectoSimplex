<?php

namespace App\Http\Controllers;

use App\Models\Cliente; // Importa el modelo Cliente
use App\Models\Instructor;
use App\Models\Proveedor;
use App\Models\Activo;
use Barryvdh\DomPDF\Facade\Pdf; // Importa el Facade de PDF
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function pdfClientes()
    {
        // Obtener los clientes con los campos necesarios
        $clientes = Cliente::select('id', 'nombre', 'apellido', 'NumeroIdentificacion', 'celular', 'correo')
                   ->orderBy('id', 'ASC')
                   ->get();
        
        // Cargar la vista del PDF y pasarle los datos de los clientes
        $pdf = Pdf::loadView('pdf.clientes', ['clientes' => $clientes]);
        
        // Configuración de tamaño de papel
        $pdf->setPaper('A4', 'portrait');
        
        // Generar el archivo PDF y devolverlo para visualización
        return $pdf->stream('clientes.pdf');
    }

    public function pdfInstructores()
    {
    // Obtener los instructores con los campos necesarios
    $instructores = Instructor::select('id', 'nombre', 'apellido', 'Identificacion', 'celular', 'correo')
                  ->orderBy('id', 'ASC')
                  ->get();
    
    // Cargar la vista del PDF y pasarle los datos de los instructores
    $pdf = Pdf::loadView('pdf.instructores', ['instructores' => $instructores]);
    
    // Configuración de tamaño de papel
    $pdf->setPaper('A4', 'portrait');
    
    // Generar el archivo PDF y devolverlo para visualización
    return $pdf->stream('instructores.pdf');
    }

    public function pdfProveedores()
{
    // Obtener los proveedores con los campos necesarios
    $proveedores = Proveedor::select('id', 'razon_social', 'NIT', 'contacto')
                   ->orderBy('id', 'ASC')
                   ->get();
    
    // Cargar la vista del PDF y pasarle los datos de los proveedores
    $pdf = Pdf::loadView('pdf.proveedores', ['proveedores' => $proveedores]);
    
    // Configuración de tamaño de papel
    $pdf->setPaper('A4', 'portrait');
    
    // Generar el archivo PDF y devolverlo para visualización
    return $pdf->stream('proveedores.pdf');
}

    public function pdfActivos()
    {
    // Obtener los activos con los campos necesarios e incluir al proveedor relacionado
    $activos = Activo::select('id', 'nombre', 'serial', 'estado', 'proveedor_id')
               ->with('proveedor:id,razon_social') // Trae solo el campo necesario del proveedor
               ->orderBy('id', 'ASC')
               ->get();

    // Cargar la vista del PDF y pasarle los datos de los activos y sus proveedores
    $pdf = Pdf::loadView('pdf.activos', ['activos' => $activos]);
    
    // Configuración de tamaño de papel
    $pdf->setPaper('A4', 'portrait');
    
    // Generar el archivo PDF y devolverlo para visualización
    return $pdf->stream('activos.pdf');
    }



}
