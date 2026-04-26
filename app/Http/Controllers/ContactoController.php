<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactoMailable;

class ContactoController extends Controller
{
    public function enviar(Request $request)
    {
        // Validar los datos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|min:10|max:1000',
        ]);

        // Enviar correo
        $correo = new ContactoMailable($request->all());
        Mail::to('hfernandovalencia@estudiante.uniajc.edu.co')->send($correo); // Cambia por tu email

        // Retornar respuesta JSON para AJAX
        return response()->json([
            'success' => true,
            'message' => '¡Mensaje enviado con éxito! Nos comunicaremos contigo pronto.'
        ]);
    }
}