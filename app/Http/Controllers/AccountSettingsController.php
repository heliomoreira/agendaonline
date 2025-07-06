<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class AccountSettingsController extends Controller
{
    public function index()
    {
        return view('modules.account-settings.index', [
            'user' => auth()->user(),
            'tenant' => auth()->user()->tenant,
        ]);
    }

    public function updateAccount(Request $request)
    {
        $tenant = Tenant::find(auth()->user()->tenant_id);
        $tenant->fill($request->all());
        $tenant->save();

        return redirect(route('account-settings.index'))->with('success', 'Account settings updated successfully.');
    }
}
