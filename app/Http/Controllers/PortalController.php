<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortalRequest;
use App\Models\Portal;
use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class PortalController extends Controller
{
    public function index()
    {
        $portal = Portal::first();
        $domain = tenant()->domains->first()?->domain;

        return view('admin.portal.form', compact('portal', 'domain'));
    }

    public function update(PortalRequest $request, $id, ImageService $imageService)
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'max:2048'], // Ou 'profile_image' ou o que usares
        ]);

        $updatePortal = Portal::find($id);

        if (!$updatePortal) {
            $updatePortal = new Portal();
        }

        if ($updatePortal == null) {
            return redirect()->route('dashboard');
        }
        $updatePortal->fill($request->except(['logo', 'payment_stripe_secret', 'payment_stripe_webhook_secret']));

        if ($request->hasFile('logo')) {
            $path = $imageService->uploadImage($request->file('logo'), 'tenants/logos', 150, 150);
            $updatePortal->logo = $path;
        }

        if ($request->hasFile('background_image')) {
            $path = $imageService->uploadImage($request->file('background_image'), 'tenants/background_image', 1920, 400);
            $updatePortal->background_image = $path;
        }

        if ($request->filled('payment_stripe_secret')) {
            $updatePortal->payment_stripe_secret = Crypt::encryptString($request->payment_stripe_secret);
        }

        if ($request->filled('payment_stripe_webhook_secret')) {
            $updatePortal->payment_stripe_webhook_secret = Crypt::encryptString($request->payment_stripe_webhook_secret);
        }

        $updatePortal->save();
        return redirect()->back()->with('success', 'Portal atualizado com sucesso.');
    }
}
