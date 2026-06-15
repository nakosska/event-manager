<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class OrganizerEventController extends Controller
{
    public function index()
    {
        $events = Event::with(['category', 'participants'])
            ->where('organizer_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('organizer.events.index', compact('events'));
    }


    public function create()
    {
        $this->authorize('create', Event::class);

        $categories = Category::all();
        return view('organizer.events.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'location_url' => 'nullable|url|max:255',
            'capacity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['slug'] = Str::slug($request->title . '-' . uniqid());
        $validated['organizer_id'] = auth()->id();
        $validated['is_published'] = false;

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        $event = Event::create($validated);

        return redirect()
            ->route('organizer.events.index')
            ->with('success', 'Мероприятие "' . $event->title . '" создано!');
    }


    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $categories = Category::all();
        return view('organizer.events.edit', compact('event', 'categories'));
    }


    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'location_url' => 'nullable|url|max:255',
            'capacity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($event->title !== $request->title) {
            $validated['slug'] = Str::slug($request->title . '-' . uniqid());
        }

        if ($request->hasFile('cover_image')) {
            if ($event->cover_image) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        $event->update($validated);

        return redirect()
            ->route('organizer.events.index')
            ->with('success', 'Мероприятие обновлено!');
    }


    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }

        $event->delete();

        return redirect()
            ->route('organizer.events.index')
            ->with('success', 'Мероприятие удалено');
    }


    public function attendees(Event $event)
    {
        $this->authorize('update', $event);

        $participants = $event->participants()
            ->orderByPivot('registered_at')
            ->paginate(20);

        return view('organizer.events.attendees', compact('event', 'participants'));
    }


    public function removeAttendee(Event $event, $userId)
    {
        $this->authorize('update', $event);

        $event->participants()->detach($userId);

        return redirect()
            ->route('organizer.events.attendees', $event)
            ->with('success', 'Участник удалён с мероприятия');
    }
}
