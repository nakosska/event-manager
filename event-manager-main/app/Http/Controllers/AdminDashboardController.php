<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Comment;

class AdminDashboardController extends Controller
{

    public function index()
    {

        $totalUsers = User::count();
        $totalEvents = Event::count();
        $publishedEvents = Event::where('is_published', true)->count();
        $totalComments = Comment::count();

        $admins = User::where('role', 'admin')->count();
        $organizers = User::where('role', 'organizer')->count();
        $participants = User::where('role', 'participant')->count();

        $latestEvents = Event::with(['category', 'organizer'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $popularEvents = Event::withCount('participants')
            ->orderBy('participants_count', 'desc')
            ->limit(5)
            ->get();


        $latestUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalEvents',
            'publishedEvents',
            'totalComments',
            'admins',
            'organizers',
            'participants',
            'latestEvents',
            'popularEvents',
            'latestUsers'
        ));
    }
}
