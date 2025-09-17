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
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $portal = Portal::find($id);

        if ($portal == null) {
            $portal = new Portal();
        }

        $portal->fill($request->except('logo'));

        if ($request->hasFile('logo')) {
            $path = $imageService->uploadImage($request->file('logo'), 'tenants/logos');
            $portal->logo = $path;
        }

        $portal->logo = '2';
        $portal->save();

        return redirect()->back()->with('success', 'Portal ' . ($id ? 'atualizado' : 'criado') . ' com sucesso.');
    }
}
