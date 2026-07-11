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
            'allow_overlap' => 'required|boolean',
        ]);

        $settings = Setting::current();
        $settings->update($request->only(['client_validation', 'allow_overlap']));

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso.');

    }
}
