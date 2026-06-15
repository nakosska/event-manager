<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class OrganizerDashboardController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        $totalEvents = Event::where('organizer_id', $user->id)->count();
        $publishedEvents = Event::where('organizer_id', $user->id)->where('is_published', true)->count();
        $draftEvents = Event::where('organizer_id', $user->id)->where('is_published', false)->count();

        $upcomingEvents = Event::with(['category', 'participants'])
            ->where('organizer_id', $user->id)
            ->where('start_datetime', '>', now())
            ->orderBy('start_datetime')
            ->limit(5)
            ->get();

        $eventsStats = Event::withCount('participants')
            ->where('organizer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();


        $totalParticipants = 0;
        foreach ($eventsStats as $event) {
            $totalParticipants += $event->participants_count;
        }

        return view('organizer.dashboard', compact(
            'totalEvents',
            'publishedEvents',
            'draftEvents',
            'totalParticipants',
            'upcomingEvents',
            'eventsStats'
        ));
    }
}
