<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServicesController extends Controller
{
    public function index()
    {
        try {
            $services = Service::all();
            return view('modules.services.index', [
                'services' => $services
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar serviços: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar a lista de serviços.'));
        }
    }

    public function form()
    {
        try {
            $service = new Service();
            return view('modules.services.form', [
                'service' => $service
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de servicee: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar o formulário.'));
        }
    }

    public function edit($id)
    {
        try {
            $service = Service::findOrFail($id);
            return view('modules.services.form', [
                'service' => $service
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao editar servicee com ID {$id}: " . $e->getMessage());
            return redirect()->route('services.index')->withErrors(__('servicee não encontrado ou ocorreu um erro.'));
        }
    }

    public function store(ServiceRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $service = new Service();
            $service->fill($request->validated());
            $service->save();

            Log::info("servicee criado com ID {$service->id}");

            return redirect()->route('services.edit', ['id' => $service->id])
                ->with('success', __('modules.service_created'));
        } catch (\Exception $e) {
            Log::error('Erro ao criar servicee: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao criar o servicee.'));
        }
    }

    public function update(ServiceRequest $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->fill($request->validated());
            $service->save();

            Log::info("servicee atualizado com ID {$service->id}");

            return redirect()->route('services.edit', ['id' => $service->id])
                ->with('success', __('modules.service_updated'));
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar servicee com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao atualizar o servicee.'));
        }
    }

    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();

            Log::info("servicee removido com ID {$id}");

            return redirect()->route('services.index')->with('success', __('modules.service_deleted'));
        } catch (\Exception $e) {
            Log::error("Erro ao eliminar servicee com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao eliminar o servicee.'));
        }
    }
}
