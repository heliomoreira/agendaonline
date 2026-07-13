<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTenantRequest;
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

        if (!$portal->enable_portal) {
            return redirect()->away('https://agendaonline.pt');
        }

        $days = [
            0 => 'sunday_hours',
            1 => 'monday_hours',
            2 => 'tuesday_hours',
            3 => 'wednesday_hours',
            4 => 'thursday_hours',
            5 => 'friday_hours',
            6 => 'saturday_hours',
        ];

        $todayHours = $portal->{$days[now()->dayOfWeek]} ?? 'Fechado';

        return view('front.portal.index', compact('tenant', 'services', 'professionals', 'portal', 'todayHours'));
    }

    public function signup()
    {
        return view('front.website.signup');
    }

    public function createTenant(RegisterTenantRequest $request)
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
            'only_own_agenda' => 0,
            'type' => 1,
            'tenant_id' => $response->getData()->data->id]);


        return view('front.website.account_created', compact('user'));
    }
}
