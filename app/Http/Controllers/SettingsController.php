<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'booking_allow_overlap' => 'required|boolean',
            'sms_send_hour' => 'required|date_format:H:i',
            'sms_advance_days' => 'required|integer|min:1|max:7',
        ]);

        $settings = Setting::current();
        $settings->update($request->only(['client_validation', 'booking_allow_overlap','sms_send_hour','sms_advance_days']));

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso.');

    }
}
