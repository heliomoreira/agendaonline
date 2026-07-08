<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Professional;


class UsersController extends Controller
{
    /**
     * List the admin users that belong to the current tenant.
     */
    public function index()
    {
        try {
            $users = User::where('tenant_id', tenant('id'))
                ->orderBy('name')
                ->get();

            return view('admin.users.index', [
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar utilizadores: ' . $e->getMessage());

            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar a lista de utilizadores.'));
        }
    }

    /**
     * Show the form to create a new admin user.
     */
    public function form()
    {
        try {
            $user = new User();

            return view('admin.users.form', [
                'user' => $user,
                'professionals' => Professional::orderBy('name')->pluck('name', 'id'),
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de utilizador: ' . $e->getMessage());

            return redirect()->back()->withErrors(__('Ocorreu um erro ao carregar o formulário.'));
        }
    }

    /**
     * Show the form to edit an existing admin user.
     */
    public function edit($id)
    {
        try {
            $user = User::where('tenant_id', tenant('id'))->findOrFail($id);

            return view('admin.users.form', [
                'user' => $user,
                'professionals' => Professional::orderBy('name')->pluck('name', 'id'),
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao editar utilizador com ID {$id}: " . $e->getMessage());

            return redirect()->route('users.index')->withErrors(__('Utilizador não encontrado ou ocorreu um erro.'));
        }
    }

    /**
     * Persist a new admin user attached to the current tenant.
     */
    public function store(UserRequest $request)
    {
        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->username = $request->input('username');
            $user->only_own_agenda = $request->boolean('only_own_agenda');
            $user->professional_id = $request->boolean('only_own_agenda')
                ? $request->input('professional_id')
                : null;
            $user->password = bcrypt($request->input('password'));
            $user->tenant_id = tenant('id');
            $user->save();

            Log::info("Utilizador criado com ID {$user->id} para o tenant " . tenant('id'));

            return redirect()->route('users.edit', ['id' => $user->id])
                ->with('success', __('Utilizador criado com sucesso.'));
        } catch (\Exception $e) {
            Log::error('Erro ao criar utilizador: ' . $e->getMessage());

            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao criar o utilizador.'));
        }
    }

    /**
     * Update an existing admin user.
     */
    public function update(UserRequest $request, $id)
    {
        try {
            $user = User::where('tenant_id', tenant('id'))->findOrFail($id);

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->username = $request->input('username');
            $user->only_own_agenda = $request->boolean('only_own_agenda');
            $user->professional_id = $request->boolean('only_own_agenda')
                ? $request->input('professional_id')
                : null;

            if ($request->filled('password')) {
                $user->password = bcrypt($request->input('password'));
            }

            $user->save();

            Log::info("Utilizador atualizado com ID {$user->id}");

            return redirect()->route('users.edit', ['id' => $user->id])
                ->with('success', __('Utilizador atualizado com sucesso.'));
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar utilizador com ID {$id}: " . $e->getMessage());

            return redirect()->back()->withInput()->withErrors(__('Ocorreu um erro ao atualizar o utilizador.'));
        }
    }

    /**
     * Delete an admin user (never the currently authenticated one).
     */
    public function destroy($id)
    {
        try {
            $user = User::where('tenant_id', tenant('id'))->findOrFail($id);

            if (Auth::id() === $user->id) {
                return redirect()->route('users.index')
                    ->withErrors(__('Não pode eliminar a sua própria conta.'));
            }

            $user->delete();

            Log::info("Utilizador removido com ID {$id}");

            return redirect()->route('users.index')->with('success', __('Utilizador eliminado com sucesso.'));
        } catch (\Exception $e) {
            Log::error("Erro ao eliminar utilizador com ID {$id}: " . $e->getMessage());

            return redirect()->back()->withErrors(__('Ocorreu um erro ao eliminar o utilizador.'));
        }
    }
}
