<?php

namespace App\Http\Controllers;

use App\Models\Portal;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PortalController extends Controller
{
    public function index()
    {
        $portal = Portal::first();
        return view('admin.portal.form', compact('portal'));
    }

    public function update(Request $request, $id, ImageService $imageService)
    {
        try {
            Log::info("Entrou");
            $request->validate([
                'logo' => ['nullable', 'image', 'max:2048'], // Ou 'profile_image' ou o que usares
            ]);

            $updatePortal = Portal::find($id);
            if ($updatePortal == null) {
                $updatePortal = new Portal();
            }

            Log::info("Entrou: 1");

            if ($updatePortal == null) {
                return redirect()->route('dashboard');
            }
            Log::info("Entrou: 2");

            $updatePortal->fill($request->except('logo'));

            Log::info("Entrou: 3");

            if ($request->hasFile('logo')) {
                $path = $imageService->uploadImage($request->file('logo'), 'tenants/logos');
                Log::info($path);
                $updatePortal->logo = $path;

                Log::info("Entrou: 4");
            }

            Log::info("Entrou: 5");

            $updatePortal->save();

            Log::info("Entrou: 6");
            return redirect()->back()->with('success', 'Portal atualizado com sucesso.');
        }
        catch (\Exception $e) {
            Log::info("Catch");
            return redirect()->back()->with('error', 'Erro ao atualizar o portal: ' . $e->getMessage());
        }
    }
}
