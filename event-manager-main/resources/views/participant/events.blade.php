@'
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-calendar-check"></i> Мои мероприятия</h1>
            <p class="text-muted">Все мероприятия, на которые вы зарегистрированы</p>
        </div>
    </div>

    <!-- Предстоящие мероприятия -->
    <div class="row mb-4">
        <div class="col-12">
            <h3>📅 Предстоящие</h3>
            <hr>
        </div>
    </div>
    <div class="row">
        @forelse($upcomingEvents ?? [] as $event)
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
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle"></i> Зарегистрирован
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
                    <i class="bi bi-info-circle"></i> Вы пока не зарегистрированы ни на одно предстоящее мероприятие
                    <br>
                    <a href="{{ route('events.index') }}" class="btn btn-primary mt-2">Посмотреть мероприятия</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Прошедшие мероприятия -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>📋 Прошедшие</h3>
            <hr>
        </div>
    </div>
    <div class="row">
        @forelse($pastEvents ?? [] as $event)
            <div class="col-md-4 mb-4">
                <div class="card event-card opacity-75">
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
                        <span class="badge bg-secondary">Завершено</span>
                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-outline-secondary btn-sm mt-3 w-100">
                            Просмотр
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-secondary text-center">
                    <i class="bi bi-info-circle"></i> У вас нет прошедших мероприятий
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
'@ | Out-File -FilePath resources\views\participant\events.blade.php -Encoding UTF8