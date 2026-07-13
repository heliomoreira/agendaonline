<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query();

        // Pesquisa por destinatário / texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('destinatary', 'like', "%{$search}%")
                    ->orWhere('text', 'like', "%{$search}%");
            });
        }

        // Tipo de destinatário
        if ($request->filled('recipient_type')) {
            $query->where('recipient_type', $request->recipient_type);
        }

        // Estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Intervalo de datas do serviço
        if ($request->filled('date_from')) {
            $query->whereDate('service_day', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('service_day', '<=', $request->date_to);
        }

        $notifications = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.notifications.index', compact('notifications'));
    }
}
