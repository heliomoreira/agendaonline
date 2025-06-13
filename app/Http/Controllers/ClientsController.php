<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientsController extends Controller
{
    public function index()
    {
        return view('modules.clients.index');
    }

    public function form()
    {
        return view('modules.clients.form');
    }
}
