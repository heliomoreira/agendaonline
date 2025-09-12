<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Services\ImageService;

class AccountSettingsController extends Controller
{
    public function index()
    {
        return view('admin.account-settings.index', [
            'user' => auth()->user(),
            'tenant' => auth()->user()->tenant,
        ]);
    }

    public function updateAccount(Request $request, ImageService $imageService)
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'max:2048'], // Ou 'profile_image' ou o que usares
        ]);

        $tenant = Tenant::find(auth()->user()->tenant_id);

        $tenant->fill($request->except('logo'));

        if ($request->hasFile('logo')) {
            $path = $imageService->uploadImage($request->file('logo'), 'tenants/logos');
            $tenant->logo = $path;
        }

        $tenant->save();

        return redirect(route('account-settings.index'))->with('success', 'Account settings updated successfully.');
    }
}
