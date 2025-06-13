<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProfessionalRequest;
use App\Models\Professional;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfessionalsController extends Controller
{
    public function index()
    {
        try {
            $professionals = Professional::all();
            return view('modules.professionals.index', [
                'professionals' => $professionals
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar Profissionais: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar a lista de Profissionais.'));
        }
    }

    public function form()
    {
        try {
            $professional = new Professional();
            return view('modules.professionals.form', [
                'professional' => $professional
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de professional: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar o formulário.'));
        }
    }

    public function edit($id)
    {
        try {
            $professional = Professional::findOrFail($id);

            $services = Service::all();

            return view('modules.professionals.form', [
                'professional' => $professional,
                'services' => $services
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao editar professional com ID {$id}: " . $e->getMessage());
            return redirect()->route('professionals.index')->withErrors(__('professional não encontrado ou ocorreu um erro.'));
        }
    }

    public function store(ProfessionalRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $professional = new Professional();
            $professional->fill($request->all());
            $professional->save();

            Log::info("professional criado com ID {$professional->id}");

            return redirect()->route('professionals.edit', ['id' => $professional->id])
                ->with('success', __('modules.professional_created'));
        } catch (\Exception $e) {
            Log::error('Erro ao criar professional: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao criar o professional.'));
        }
    }

    public function update(ProfessionalRequest $request, $id)
    {
        try {
            $professional = Professional::findOrFail($id);
            $professional->fill($request->all());
            $professional->save();

            Log::info("Professional atualizado com ID {$professional->id}");

            return redirect()->route('professionals.edit', ['id' => $professional->id])
                ->with('success', __('modules.professional_updated'));
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar professional com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao atualizar o professional.'));
        }
    }

    public function destroy($id)
    {
        try {
            $professional = Professional::findOrFail($id);
            $professional->delete();

            Log::info("professional removido com ID {$id}");

            return redirect()->route('professionals.index')->with('success', __('modules.professional_deleted'));
        } catch (\Exception $e) {
            Log::error("Erro ao eliminar professional com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao eliminar o professional.'));
        }
    }
}
