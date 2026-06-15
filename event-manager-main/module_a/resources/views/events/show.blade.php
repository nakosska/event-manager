@'
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <!-- Основная информация -->
            <div class="card mb-4">
                @if($event->cover_image)
                    <img src="{{ asset('storage/' . $event->cover_image) }}" class="card-img-top" alt="{{ $event->title }}">
                @endif
                <div class="card-body">
                    <h1 class="card-title">{{ $event->title }}</h1>
                    
                    @if($event->category)
                        <span class="badge category-{{ strtolower($event->category->name) }} category-badge mb-3">
                            {{ $event->category->name }}
                        </span>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <i class="bi bi-calendar3"></i>
                            <strong>Начало:</strong> {{ $event->start_datetime->format('d.m.Y H:i') }}
                        </div>
                        <div class="col-md-6">
                            <i class="bi bi-calendar-check"></i>
                            <strong>Окончание:</strong> {{ $event->end_datetime->format('d.m.Y H:i') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <i class="bi bi-geo-alt"></i>
                            <strong>Место:</strong> {{ $event->location }}
                        </div>
                        <div class="col-md-6">
                            <i class="bi bi-people"></i>
                            <strong>Участники:</strong> {{ $event->participants()->count() }}/{{ $event->capacity ?? '∞' }}
                        </div>
                    </div>

                    @if($event->price > 0)
                        <div class="mb-3">
                            <i class="bi bi-currency-ruble"></i>
                            <strong>Стоимость:</strong> {{ $event->price }} ₽
                        </div>
                    @endif

                    <div class="mb-3">
                        <i class="bi bi-person"></i>
                        <strong>Организатор:</strong> {{ $event->organizer->name ?? 'Не указан' }}
                    </div>

                    <div class="mb-4">
                        <h5>Описание</h5>
                        <p>{{ $event->description }}</p>
                    </div>

                    <!-- Кнопки действий -->
                    <div class="d-flex gap-2">
                        @auth
                            @if(!$userRegistered && !$isFull)
                                <form action="{{ route('events.register', $event) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Зарегистрироваться
                                    </button>
                                </form>
                            @elseif($userRegistered)
                                <form action="{{ route('events.unregister', $event) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-x-circle"></i> Отменить регистрацию
                                    </button>
                                </form>
                            @elseif($isFull)
                                <button class="btn btn-secondary" disabled>
                                    <i class="bi bi-person-x"></i> Мест нет
                                </button>
                            @endif

                            @can('update', $event)
                                <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> Редактировать
                                </a>
                            @endcan
                        @endauth

                        <a href="{{ route('events.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </div>

            <!-- Комментарии -->
            <div class="card">
                <div class="card-header">
                    <h5>Комментарии ({{ $event->comments->count() }})</h5>
                </div>
                <div class="card-body">
                    @auth
                        <form action="{{ route('events.comments.store', $event) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <textarea name="body" class="form-control" rows="3" placeholder="Написать комментарий..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Отправить</button>
                        </form>
                    @endauth

                    @foreach($event->comments as $comment)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $comment->user->name }}</strong>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $comment->body }}</p>
                            @can('delete', $comment)
                                <form action="{{ route('events.comments.destroy', [$event, $comment]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            @endcan
                        </div>
                    @endforeach

                    @if($event->comments->isEmpty())
                        <p class="text-muted text-center">Нет комментариев</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Информация</h5>
                </div>
                <div class="card-body">
                    <p><strong>Статус:</strong> 
                        @if($event->is_published)
                            <span class="badge bg-success">Опубликовано</span>
                        @else
                            <span class="badge bg-warning">Черновик</span>
                        @endif
                    </p>
                    
                    @if($event->location_url)
                        <p><a href="{{ $event->location_url }}" target="_blank" class="btn btn-outline-primary w-100">
                            <i class="bi bi-map"></i> Открыть на карте
                        </a></p>
                    @endif

                    @can('update', $event)
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="{{ route('organizer.events.attendees', $event) }}" class="btn btn-info">
                                <i class="bi bi-people"></i> Список участников
                            </a>
                            <form action="{{ route('organizer.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Удалить мероприятие?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-trash"></i> Удалить
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
'@ | Out-File -FilePath resources\views\events\show.blade.php -Encoding UTF8