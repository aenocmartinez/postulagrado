<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index($procesoID)
    {
        return view('calendarios.index');
    }
}
