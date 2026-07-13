<?php

namespace App\Http\Middleware;

use App\Models\Portal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $portal = Portal::first();

        if (!$portal || !$portal->enable_portal) {
            return redirect()->away('https://agendaonline.pt');
        }

        return $next($request);
    }
}
