<?php

namespace App\Http\Controllers;

use App\Http\Requests\BetaSignupRequest;
use App\Http\Requests\ContactRequest;
use App\Mail\BetaSignupConfirmation;
use App\Mail\BetaSignupReceived;
use App\Mail\ContactMessage;
use App\Models\BetaSignup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class WebsiteController extends Controller
{
    public function contacto(ContactRequest $request): JsonResponse|RedirectResponse
    {
        $ok = 'Obrigado! Recebemos a sua mensagem e respondemos em breve.';

        // Honeypot preenchido: é um bot. Fingimos sucesso e não enviamos nada.
        if ($request->filled('website')) {
            return $this->sucesso($request, $ok, 'contactos', 'contacto_status');
        }

        $dados = $request->validated();

        try {
            Mail::to(config('agendaonline.notify_to'))
                ->send(new ContactMessage($dados['nome'], $dados['email'], $dados['mensagem']));
        } catch (Throwable $e) {
            report($e);

            return $this->erro(
                $request,
                'Não foi possível enviar a mensagem. Escreva-nos para agendaonline@bitsis.pt.',
                'contacto'
            );
        }

        return $this->sucesso($request, $ok, 'contactos', 'contacto_status');
    }

    public function beta(BetaSignupRequest $request): JsonResponse|RedirectResponse
    {
        // A mesma resposta para inscrições novas e repetidas, para não revelar que emails já estão inscritos.
        $ok = 'Inscrição feita! Enviámos-lhe um email de confirmação e avisamos assim que puder criar a sua conta.';

        if ($request->filled('website')) {
            return $this->sucesso($request, $ok, 'inscricao-beta', 'beta_status');
        }

        $dados = $request->validated();

        $signup = BetaSignup::firstOrCreate(
            ['email' => $dados['email']],
            [
                'nome' => $dados['nome'],
                'negocio' => $dados['negocio'] ?? null,
                'tipo_negocio' => $dados['tipo_negocio'],
                'profissionais' => $dados['profissionais'] ?? null,
                'consentimento_em' => now(),
                'ip' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            ]
        );

        if ($signup->wasRecentlyCreated) {
            // A inscrição já está guardada; uma falha de email não deve dar erro ao visitante.
            try {
                Mail::to($signup->email)->send(new BetaSignupConfirmation($signup));
            } catch (Throwable $e) {
                report($e);
            }

            try {
                Mail::to(config('agendaonline.notify_to'))->send(new BetaSignupReceived($signup));
            } catch (Throwable $e) {
                report($e);
            }
        }

        return $this->sucesso($request, $ok, 'inscricao-beta', 'beta_status');
    }

    private function sucesso(Request $request, string $mensagem, string $ancora, string $flash): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $mensagem]);
        }

        return redirect()->to(url('/').'#'.$ancora)->with($flash, $mensagem);
    }

    private function erro(Request $request, string $mensagem, string $bag): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $mensagem], 503);
        }

        return back()->withInput()->withErrors(['geral' => $mensagem], $bag);
    }
}
