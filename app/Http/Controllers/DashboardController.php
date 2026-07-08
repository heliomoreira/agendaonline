<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $nextEvents = Agenda::with(['service', 'professional'])
            ->visibleTo(auth()->user())
            ->upcoming()
            ->get();

        return view('admin.dashboard.index', [
            'nextEvents' => $nextEvents,
        ]);
    }
}
