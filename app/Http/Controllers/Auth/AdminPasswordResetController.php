<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AdminPasswordResetMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPasswordResetController extends Controller
{
    // -------------------------------------------------------
    // Passo 1 — Formulário "Esqueci a password"
    // -------------------------------------------------------

    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'O email é obrigatório.',
            'email.email'    => 'Introduza um endereço de email válido.',
        ]);

        // Não revelar se o email existe ou não (segurança)
        $admin = User::where('email', $request->email)->first();

        if ($admin) {
            // Apagar token anterior se existir
            DB::table('admin_password_reset_tokens')
                ->where('email', $admin->email)
                ->delete();

            $token = Str::random(64);

            DB::table('admin_password_reset_tokens')->insert([
                'email'      => $admin->email,
                'token'      => Hash::make($token),
                'created_at' => Carbon::now(),
            ]);

            $resetUrl = route('admin.password.reset.form', [
                'token' => $token,
                'email' => $admin->email,
            ]);

            Mail::to($admin->email)->send(new AdminPasswordResetMail($resetUrl));
        }

        return back()->with(
            'status',
            'Se o email existir na nossa base de dados, receberá um link de recuperação em breve.'
        );
    }

    // -------------------------------------------------------
    // Passo 2 — Formulário de nova password
    // -------------------------------------------------------

    public function showResetForm(Request $request): View
    {
        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email'                 => ['required', 'email'],
            'token'                 => ['required'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required'             => 'O email é obrigatório.',
            'email.email'                => 'Introduza um endereço de email válido.',
            'token.required'             => 'Token inválido.',
            'password.required'          => 'A password é obrigatória.',
            'password.min'               => 'A password deve ter pelo menos :min caracteres.',
            'password.confirmed'         => 'A confirmação da password não coincide.',
        ]);

        $record = DB::table('admin_password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Validar token e expiração (60 minutos)
        if (
            ! $record ||
            ! Hash::check($request->token, $record->token) ||
            Carbon::parse($record->created_at)->addMinutes(60)->isPast()
        ) {
            return back()
                ->withInput(['email' => $request->email])
                ->withErrors(['email' => 'Este link de recuperação é inválido ou já expirou.']);
        }

        $admin = User::where('email', $request->email)->first();

        if (! $admin) {
            return back()->withErrors(['email' => 'Não foi possível encontrar a conta.']);
        }

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        // Apagar o token usado
        DB::table('admin_password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('login')
            ->with('status', 'Password alterada com sucesso. Pode agora iniciar sessão.');
    }
}
