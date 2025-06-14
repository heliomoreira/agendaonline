<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $nextEvents = Agenda::with(['service','professional'])->upcoming()->get();

        return view('dashboard', [
            'nextEvents' => $nextEvents
        ]);
    }
}
