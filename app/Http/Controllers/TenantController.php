<?php

namespace App\Http\Controllers;

use App\Models\Portal;
use App\Models\Professional;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{

    /*
     * Display form to schedule service
     *
     */
    public function index()
    {
        $tenant = Tenant::find(tenant('id'));
        $services = Service::active()->orderBy('order', 'asc')->get();
        $professionals = Professional::all();
        $portal = Portal::first();

        return view('front.portal.index', compact('tenant', 'services', 'professionals', 'portal'));
    }

    public function signup()
    {
        return view('front.website.signup');
    }

    public function createTenant(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'g-recaptcha-response' => 'required|recaptchav3:register,0.5'
        ]);

        $tenant = new TenantService();
        $response = $tenant->createTenant($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'tenant_id' => $response->getData()->data->id]);


        return view('front.website.account_created', compact('user'));
    }
}
