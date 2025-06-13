<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return view('modules.clients.index', [
            'clients' => $clients
        ]);
    }

    public function form()
    {
        $client = new Client();
        return view('modules.clients.form', [
            'client' => $client
        ]);
    }

    public function edit($id)
    {
        $client = Client::find($id);
        return view('modules.clients.form', [
            'client' => $client
        ]);
    }

    public function store(ClientRequest $request)
    {
        dd("jhello");
        $client = new Client();
        $client->fill($request->all());
        $client->save();

        return redirect()->route('clients.edit', ['id' => $client->id])
            ->with('success', __('modules.client_created'));
    }

    public function update(ClientRequest $request, $id)
    {
        $client = Client::find($id);
        $client->fill($request->all());
        $client->id = $id;
        $client->save();

        return redirect()->route('clients.edit', ['id' => $client->id])
            ->with('success', __('modules.client_updated'));
    }

    public function destroy()
    {

    }
}
