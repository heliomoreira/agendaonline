<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // cria a view resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $tenant = tenant(); // ← agora funciona, porque o middleware de tenancy já está ativo

        if (! $tenant) {
            abort(403, 'Tenant não identificado.');
        }

        $credentials = $request->only('email', 'password');
        $credentials['tenant_id'] = $tenant->id; // ← só autentica se for o tenant certo

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas ou não pertencem a este tenant.',
        ]);
    }
}
