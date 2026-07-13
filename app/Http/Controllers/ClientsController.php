<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Client;
use App\Models\SmsTemplate;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $clients = Client::query()
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->search;
                    $q->where(function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('phone_1', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->when($request->filled('city'),
                    fn ($q) => $q->where('city', 'like', "%{$request->city}%"))
                ->when($request->filled('is_minor'),
                    fn ($q) => $q->where('is_minor', $request->is_minor))
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString();

            return view('admin.clients.index', [
                'clients' => $clients
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar clientes: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar a lista de clientes.'));
        }
    }

    public function form()
    {
        try {
            $client = new Client();
            $templates = SmsTemplate::where('status', 1)->pluck('title', 'id');
            return view('admin.clients.form', [
                'client' => $client,
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de cliente: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar o formulário.'));
        }
    }

    public function edit($id)
    {
        try {
            $client = Client::findOrFail($id);
            $templates = SmsTemplate::where('status', 1)->pluck('title', 'id');
            return view('admin.clients.form', [
                'client' => $client,
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao editar cliente com ID {$id}: " . $e->getMessage());
            return redirect()->route('clients.index')->withErrors(__('Cliente não encontrado ou ocorreu um erro.'));
        }
    }

    public function store(CustomerRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $client = CustomerService::findOrCreate($request->validated());

            return redirect()->route('clients.edit', ['id' => $client->id])
                ->with('success', __('modules.client_created'));
        } catch (\Exception $e) {
            Log::error('Erro ao criar cliente: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao criar o cliente.'));
        }
    }

    public function update(CustomerRequest $request, $id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->fill($request->all());
            $client->save();

            Log::info("Cliente atualizado com ID {$client->id}");

            return redirect()->route('clients.edit', ['id' => $client->id])
                ->with('success', __('modules.client_updated'));
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar cliente com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao atualizar o cliente.'));
        }
    }

    public function destroy($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->delete();

            Log::info("Cliente removido com ID {$id}");

            return redirect()->route('clients.index')->with('success', __('modules.client_deleted'));
        } catch (\Exception $e) {
            Log::error("Erro ao eliminar cliente com ID {$id}: " . $e->getMessage());
            return redirect()->back()->withErrors(__('Ocorreu um erro ao eliminar o cliente.'));
        }
    }

    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $clients = Client::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('phone_1', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email', 'phone_1', 'phone_1_country_code']);

        return response()->json($clients);
    }
}
