<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Tenant;

class TenantService
{
    public function createTenant($request): \Illuminate\Http\JsonResponse
    {

        $tenantId = Helper::normalizeString($request['username']);
        $virtualMin = new VirtualminService();

        $tenant = Tenant::create([
            'id' => $tenantId,
            'name' => $request['name'],
            'email' => $request['email'],
            'username' => $tenantId,
            'plan_id' => 1,
        ]);

        $tenant->domains()->create(['domain' => $tenantId . '.' . config('tenancy.tenant_domain')]);

        return response()->json(['message' => 'success', 'data' => $tenant], 200);
    }

}
