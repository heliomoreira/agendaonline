<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(){
        $unpaid = Agenda::with(['service','client','paymentStatus'])->where('payment_status_id', 1)->get();
        return view('admin.reports.index', compact('unpaid'));
    }
}
