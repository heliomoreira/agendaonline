<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountSettingsController extends Controller
{
    public function index()
    {
        return view ('modules.account-settings.index', [
            'user' => auth()->user(),
            'tenant' => auth()->user()->tenant,
        ]);
    }
}
