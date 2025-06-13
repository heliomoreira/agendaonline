<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientsController extends Controller
{
    public function index()
    {
        try {
            $clients = Client::all();
            return view('modules.clients.index', [
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
            return view('modules.clients.form', [
                'client' => $client
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
            return view('modules.clients.form', [
                'client' => $client
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao editar cliente com ID {$id}: " . $e->getMessage());
            return redirect()->route('clients.index')->withErrors(__('Cliente não encontrado ou ocorreu um erro.'));
        }
    }

    public function store(CustomerRequest $request)
    {
        try {
            $client = new Client();
            $client->fill($request->all());
            $client->save();

            Log::info("Cliente criado com ID {$client->id}");

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
}
