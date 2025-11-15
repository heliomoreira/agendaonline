<?php

namespace App\Http\Controllers;

use App\Models\Portal;
use App\Services\ImageService;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function index()
    {
        $portal = Portal::first();
        return view('admin.portal.form', compact('portal'));
    }

    public function update(Request $request, $id, ImageService $imageService)
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'max:2048'], // Ou 'profile_image' ou o que usares
        ]);

        $updatePortal = Portal::find($id);

        if (!$updatePortal) {
            $updatePortal = new Portal();
        }

        if( $updatePortal == null){
            return redirect()->route('dashboard');
        }
        $updatePortal->fill($request->except('logo'));

        if ($request->hasFile('logo')) {
            $path = $imageService->uploadImage($request->file('logo'), 'tenants/logos');
            $updatePortal->logo = $path;
        }

        $updatePortal->save();
        return redirect()->back()->with('success', 'Portal atualizado com sucesso.');
    }
}
