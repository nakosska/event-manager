<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{

    public function index(Request $request)
    {
        $query = Event::with(['category', 'organizer'])
            ->where('is_published', true);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_filter')) {
            $now = now();
            $filter = $request->date_filter;

            switch ($filter) {
                case 'today':
                    $query->whereDate('start_datetime', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('start_datetime', [$now, $now->copy()->addWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('start_datetime', [$now, $now->copy()->addMonth()]);
                    break;
                case 'upcoming':
                    $query->where('start_datetime', '>', $now);
                    break;
            }
        }

        $events = $query->orderBy('start_datetime')->paginate(9);
        $categories = Category::all();

        return view('events.index', compact('events', 'categories'));
    }


    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $event->load(['category', 'organizer', 'participants', 'comments.user']);
        $userRegistered = auth()->check() ? $event->isUserRegistered(auth()->id()) : false;
        $isFull = $event->isFull();
        $availableSeats = $event->getAvailableSeats();

        return view('events.show', compact('event', 'userRegistered', 'isFull', 'availableSeats'));
    }

    public function register(Event $event)
    {

        $this->authorize('register', $event);

        if ($event->isFull()) {
            return response()->json(['error' => 'Нет свободных мест'], 422);
        }

        if ($event->isUserRegistered(auth()->id())) {
            return response()->json(['error' => 'Вы уже зарегистрированы'], 422);
        }

        auth()->user()->registeredEvents()->attach($event->id, ['registered_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Вы успешно зарегистрированы!'
        ]);
    }

    public function unregister(Event $event)
    {

        $this->authorize('unregister', $event);

        auth()->user()->registeredEvents()->detach($event->id);

        return response()->json([
            'success' => true,
            'message' => 'Регистрация отменена'
        ]);
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
            ->with('success', 'Мероприятие "' . $event->title . '" успешно создано!');
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
            // Удаляем старую обложку
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

        $participants = $event->participants()->orderByPivot('registered_at')->get();

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
