<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $nextEvents = Agenda::with(['service', 'professional'])
            ->visibleTo(auth()->user())
            ->upcoming()
            ->whereBetween('day', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->get();


        return view('admin.dashboard.index', [
            'nextEvents' => $nextEvents,
            'servicesToday' => Agenda::whereDate('day', today())->count(),
            'servicesWeek' => Agenda::whereBetween('day', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'servicesMonth' => Agenda::whereBetween('day', [now()->startOfMonth(), now()->endOfMonth()])->count()
        ]);
    }
}
