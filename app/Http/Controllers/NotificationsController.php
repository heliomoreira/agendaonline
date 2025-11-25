<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('tenant_id', tenant('id'))
            ->where('service_day','>=', Carbon::today())
            ->orderBy('service_day')
            ->get();

        return view('admin.notifications.index', [
            'notifications' => $notifications
        ]);
    }
}
