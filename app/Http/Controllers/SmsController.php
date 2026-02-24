<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function index()
    {
        $tenant = tenant();
        $notifications = Notification::where('tenant_id', tenant('id'))
            ->paginate(25);

        return view('admin.sms.index', [
            'tenant' => $tenant,
            'notifications' => $notifications
        ]);
    }

    public function update(Request $request)
    {
        $tenant = tenant();
        $tenant->sms_status = $request->sms_status;
        $tenant->sms_send_hour = $request->sms_send_hour;
        $tenant->save();
        return redirect()->back()->with('success', 'Opções SMS atualizadas com sucesso.');
    }
}
