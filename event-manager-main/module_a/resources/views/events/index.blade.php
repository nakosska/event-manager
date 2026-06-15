# Создаём файл index.blade.php
@'
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Мероприятия</h1>
        </div>
        <div class="col-md-4 text-end">
            @auth
                @if(auth()->user()->isOrganizer() || auth()->user()->isAdmin())
                    <a href="{{ route('organizer.events.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Создать мероприятие
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <!-- Фильтры -->
    <div class="row mb-4">
        <div class="col-md-12">
            <form method="GET" action="{{ route('events.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Поиск..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">Все категории</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="date_filter" class="form-select">
                        <option value="">Все даты</option>
                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Сегодня</option>
                        <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>Эта неделя</option>
                        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>Этот месяц</option>
                        <option value="upcoming" {{ request('date_filter') == 'upcoming' ? 'selected' : '' }}>Предстоящие</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Фильтр</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Список мероприятий -->
    <div class="row">
        @forelse($events ?? [] as $event)
            <div class="col-md-4 mb-4">
                <div class="card event-card">
                    @if($event->cover_image)
                        <img src="{{ asset('storage/' . $event->cover_image) }}" class="card-img-top event-cover" alt="{{ $event->title }}">
                    @else
                        <div class="card-img-top event-cover bg-secondary d-flex align-items-center justify-content-center text-white">
                            <i class="bi bi-calendar-event" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-text text-muted small">
                            <i class="bi bi-calendar3"></i> {{ $event->start_datetime->format('d.m.Y H:i') }}
                        </p>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt"></i> {{ $event->location }}
                        </p>
                        @if($event->category)
                            <span class="badge category-{{ strtolower($event->category->name) }} category-badge">
                                {{ $event->category->name }}
                            </span>
                        @endif
                        <span class="badge bg-info">
                            <i class="bi bi-people"></i> {{ $event->participants()->count() }}/{{ $event->capacity ?? '∞' }}
                        </span>
                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary btn-sm mt-3 w-100">
                            Подробнее
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> Мероприятий пока нет
                </div>
            </div>
        @endforelse
    </div>

    <!-- Пагинация -->
    <div class="row">
        <div class="col-12">
            {{ $events->links() ?? '' }}
        </div>
    </div>
</div>
@endsection
'@ | Out-File -FilePath resources\views\events\index.blade.php -Encoding UTF8