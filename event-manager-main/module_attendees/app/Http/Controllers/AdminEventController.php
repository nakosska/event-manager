<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class AdminEventController extends Controller
{

    public function index()
    {
        $events = Event::with(['category', 'organizer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.events.index', compact('events'));
    }


    public function togglePublish(Event $event)
    {
        $event->is_published = !$event->is_published;
        $event->save();

        $status = $event->is_published ? 'опубликовано' : 'скрыто';

        return redirect()
            ->back()
            ->with('success', "Мероприятие \"{$event->title}\" {$status}!");
    }


    public function show(Event $event)
    {
        $event->load(['category', 'organizer', 'participants', 'comments.user']);

        return view('admin.events.show', compact('event'));
    }


    public function destroy(Event $event)
    {
        if ($event->cover_image) {
            \Storage::disk('public')->delete($event->cover_image);
        }

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Мероприятие удалено!');
    }
}
