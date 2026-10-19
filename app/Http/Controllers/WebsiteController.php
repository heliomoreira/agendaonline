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
    public function contact(ContactRequest $request): JsonResponse|RedirectResponse
    {
        $successMessage = 'Obrigado! Recebemos a sua mensagem e respondemos em breve.';

        // Honeypot filled in: it's a bot. Pretend it worked and send nothing.
        if ($request->filled('website')) {
            return $this->success($request, $successMessage, 'contactos', 'contact_status');
        }

        $data = $request->validated();

        try {
            Mail::to(config('agendaonline.notify_to'))
                ->send(new ContactMessage($data['name'], $data['email'], $data['message']));
        } catch (Throwable $e) {
            report($e);

            return $this->error(
                $request,
                'Não foi possível enviar a mensagem. Escreva-nos para agendaonline@bitsis.pt.',
                'contact'
            );
        }

        return $this->success($request, $successMessage, 'contactos', 'contact_status');
    }

    public function betaSignup(BetaSignupRequest $request): JsonResponse|RedirectResponse
    {
        // Same response for new and repeat signups, so we don't reveal which emails are already registered.
        $successMessage = 'Inscrição feita! Enviámos-lhe um email de confirmação e avisamos assim que puder criar a sua conta.';

        if ($request->filled('website')) {
            return $this->success($request, $successMessage, 'inscricao-beta', 'beta_status');
        }

        $data = $request->validated();

        $signup = BetaSignup::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'business' => $data['business'] ?? null,
                'business_type' => $data['business_type'],
                'professionals' => $data['professionals'] ?? null,
                'consented_at' => now(),
                'ip' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            ]
        );

        if ($signup->wasRecentlyCreated) {
            // The signup is already saved; an email failure shouldn't show an error to the visitor.
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

        return $this->success($request, $successMessage, 'inscricao-beta', 'beta_status');
    }

    private function success(Request $request, string $message, string $anchor, string $flashKey): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->to(url('/').'#'.$anchor)->with($flashKey, $message);
    }

    private function error(Request $request, string $message, string $errorBag): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 503);
        }

        return back()->withInput()->withErrors(['general' => $message], $errorBag);
    }
}
