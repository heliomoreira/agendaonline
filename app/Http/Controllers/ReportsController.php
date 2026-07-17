<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        $unpaid = Agenda::with(['service', 'client', 'paymentStatus'])->where('payment_status_id', 1)->orderBy('day')->where('day', '<', Carbon::today())->get();
        return view('admin.reports.index', compact('unpaid'));
    }
}
