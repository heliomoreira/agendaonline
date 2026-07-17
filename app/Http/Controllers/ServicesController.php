<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Models\SmsTemplate;
use App\Models\Tenant;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServicesController extends Controller
{

    public function __construct(protected ImageService $imageService)
    {

    }

    public function index(Request $request)
    {
        try {
            $services = Service::query()
                ->when($request->filled('search'),
                    fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
                ->when($request->filled('status'),
                    fn ($q) => $q->where('status', $request->status))
                ->orderBy('order')
                ->paginate(15)
                ->withQueryString();

            return view('admin.services.index', compact('services'));
        } catch (\Exception $e) {
            Log::error('Erro ao listar serviços: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar a lista de serviços.'));
        }
    }

    public function form()
    {
        try {
            $service = new Service();
            $templates = SmsTemplate::where('status', 1)->pluck('title', 'id');
            return view('admin.services.form', [
                'service' => $service,
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de service: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar o formulário.'));
        }
    }

    public function edit($id)
    {
        try {
            $service = Service::findOrFail($id);
            $templates = SmsTemplate::where('status', 1)->pluck('title', 'id');
            return view('admin.services.form', [
                'service' => $service,
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao editar service com ID {$id}: " . $e->getMessage());
            return redirect()->route('services.index')->withErrors(__('service não encontrado ou ocorreu um erro.'));
        }
    }

    public function store(ServiceRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $service = new Service();
            $service->fill($request->validated());

            if ($request->hasFile('image')) {
                $image = $this->imageService->uploadImage($request->file('image'), 'tenants/services/images');
                $service->image = $image;

            }
            $service->save();

            return redirect()->route('services.edit', ['id' => $service->id])
                ->with('success', __('modules.service_created'));
        } catch (\Exception $e) {
            Log::error('Erro ao criar service: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao criar o service.'));
        }
    }

    public function update(ServiceRequest $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->fill($request->validated());

            if ($request->hasFile('image')) {
                $image = $this->imageService->uploadImage($request->file('image'), 'tenants/services/images');
                $service->image = $image;
            }
            $service->save();
            return redirect()->route('services.edit', ['id' => $service->id])
                ->with('success', __('modules.service_updated'));
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar service com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao atualizar o service.'));
        }
    }

    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);

            if ($service->appointments()->exists()) {
                return redirect()->back()->withErrors(__('Não é possível remover este serviço porque tem agendamentos associados. Desactive-o em vez de o remover.'));
            }

            $service->delete();

            Log::info("service removido com ID {$id}");

            return redirect()->route('services.index')->with('success', __('modules.service_deleted'));
        } catch (\Exception $e) {
            Log::error("Erro ao eliminar service com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao eliminar o service.'));
        }
    }
}
