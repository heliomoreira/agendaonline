<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\BetaSignup;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        $unpaid = Agenda::with(['service', 'client', 'paymentStatus'])->where('payment_status_id', 1)->orderBy('day')->where('day', '<', Carbon::today())->get();
        return view('admin.reports.index', compact('unpaid'));
    }

    public function getBetas(){
        $betas = BetaSignup::on(config('tenancy.database.central_connection'))->get();
        return view('admin.betas', compact('betas'));
    }
}
