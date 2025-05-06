<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgramaAcademicoController extends Controller
{
    
    public function index()
    {
        return view('programa_academico.dashboard.index');
    }
}
