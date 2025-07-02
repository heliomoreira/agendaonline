<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function signup()
    {
        return view('website.signup');
    }

    public function createTenant(Request $request)
    {

        $tenant = new TenantService();
        $tenant->createTenant($request->all());

/*        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'tenant_id' => $tenant]);*/


        return response()->json(['message' => 'Tenant created successfully!'], 201);
    }
}
