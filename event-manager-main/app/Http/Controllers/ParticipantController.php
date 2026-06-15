<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller
{

    public function index()
    {
        $user = Auth::user();


        $upcomingEvents = $user->registeredEvents()
            ->where('start_datetime', '>', now())
            ->orderBy('start_datetime')
            ->get();


        $pastEvents = $user->registeredEvents()
            ->where('start_datetime', '<', now())
            ->orderBy('start_datetime', 'desc')
            ->get();

        return view('participant.events', compact('upcomingEvents', 'pastEvents'));
    }
}
