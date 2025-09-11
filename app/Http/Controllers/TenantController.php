<?php

namespace App\Http\Controllers;

use App\Models\Portal;
use App\Models\Professional;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Http\Request;

class TenantController extends Controller
{

    public function index()
    {
        $tenant = Tenant::find(tenant('id'));
        $services = Service::all();
        $professionals = Professional::all();
        $portal = Portal::first();

        return view('front.booking.index', compact('tenant','services','professionals','portal'));
    }

    public function signup()
    {
        return view('website.signup');
    }

    public function createTenant(Request $request)
    {

        $tenant = new TenantService();
        $response = $tenant->createTenant($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'tenant_id' => $response->getData()->data->id]);


        return view('website.account_created', compact('user'));;
    }
}
