<?php

namespace App\Http\Controllers;


class DashboardController extends Controller
{
    //
    public function index()
    {

    // Pasar el total a la vista
    return view('dashboard');
    }
}
