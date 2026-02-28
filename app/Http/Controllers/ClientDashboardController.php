<?php

// app/Http/Controllers/ClientDashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $client = auth('client')->user();

        $upcomingBookings = $client->upcomingBookings();
        $pastBookings = $client->pastBookings()->take(5);

        return view('client.dashboard', compact('client', 'upcomingBookings', 'pastBookings'));
    }

    public function bookings()
    {
        $client = auth('client')->user();

        $upcoming = $client->upcomingBookings();
        $past = $client->pastBookings();

        return view('client.bookings', compact('upcoming', 'past'));
    }

    public function show($id)
    {
        $booking = auth('client')->user()
            ->bookings()
            ->with(['service', 'professional'])
            ->findOrFail($id);

        return view('client.bookings.show', compact('booking'));
    }

    public function cancel(Request $request, $id)
    {
        $booking = auth('client')->user()
            ->bookings()
            ->findOrFail($id);

        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'Esta marcação não pode ser cancelada.');
        }

        $booking->cancel(
            $request->input('reason'),
            'client'
        );

        return redirect()->route('client.bookings')->with('success', 'Marcação cancelada.');
    }
}
