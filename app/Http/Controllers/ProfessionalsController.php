<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProfessionalRequest;
use App\Models\Professional;
use App\Models\ProfessionalWorkingHour;
use App\Models\Service;
use App\Services\ProfessionalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfessionalsController extends Controller
{
    public function index()
    {

        try {
            $professionals = ProfessionalService::list();

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
            $professional = Professional::with(['services', 'workingHours'])->findOrFail($id);
            $workingHours = ProfessionalWorkingHour::where('professional_id', $id)
                ->orderBy('weekday')
                ->orderBy('start_hour')
                ->get();
            $services = Service::all();

            return view('modules.professionals.form', [
                'professional' => $professional,
                'services' => $services,
                'workingHours' => $workingHours,
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

    public function updateServices(Request $request, $id)
    {
        try {
            $professional = Professional::findOrFail($id);

            $professional->services()->sync($request->input('services', []));

            //Log::info("Serviços atualizados para o profissional ID {$professional->id}");

            return redirect()->route('professionals.edit', $professional->id)
                ->with('success', 'Serviços atualizados com sucesso.');
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar serviços do profissional ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->withErrors('Erro ao atualizar os serviços.');
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

    public function getProfessionalsByService($id)
    {
        $professionals = Professional::whereHas('services', function ($query) use ($id) {
            $query->where('services.id', $id);
        })->get();


        return response()->json($professionals);
    }

    public function saveWorkingHours(Request $request, $professionalId)
    {

        /*  $request->validate([
              'working_hours' => 'required|array',
              'working_hours.*.weekday' => 'required|integer|between:0,6',
              'working_hours.*.start_hour' => 'required|date_format:H:i',
              'working_hours.*.end_hour' => 'required|date_format:H:i|after:working_hours.*.start_hour',
          ]);*/

        ProfessionalWorkingHour::where('professional_id', $professionalId)->delete();

        if ($request->filled('working_hours')) {
            foreach ($request->working_hours as $block) {
                if (empty($block['weekday']) || empty($block['start_hour']) || empty($block['end_hour'])) {
                    continue;
                }
                ProfessionalWorkingHour::create([
                    'professional_id' => $professionalId,
                    'weekday' => (int) $block['weekday'],
                    'start_hour' => $block['start_hour'],
                    'end_hour' => $block['end_hour'],
                ]);
            }
        }
        return redirect()->back()->with('success', 'Horários de trabalho atualizados com sucesso.');
    }
}
