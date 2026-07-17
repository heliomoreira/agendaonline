<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function index(Request $request)
    {
        $notifications = $this->notificationService->getFiltered(
            $request->only(['search', 'recipient_type', 'status', 'date_from', 'date_to', 'show_all'])
        );

        return view('admin.notifications.index', compact('notifications'));
    }
}
