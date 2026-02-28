<?php

// app/Http/Controllers/ClientAuthController.php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class ClientAuthController extends Controller
{
    public function showLogin()
    {
        return view('client.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('client')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Update last login
            Auth::guard('client')->user()->update(['last_login_at' => now()]);

            return redirect()->intended(route('client.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('client.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone_1' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        $client = Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_1' => $validated['phone_1'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($client));

        Auth::guard('client')->login($client);

        return redirect()->route('client.dashboard')->with('success', 'Conta criada com sucesso!');
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }
}
