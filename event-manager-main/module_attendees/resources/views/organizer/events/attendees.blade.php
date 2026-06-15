@'
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-people"></i> Участники мероприятия: {{ $event->title }}</h1>
            <p class="text-muted">Всего зарегистрировано: {{ $participants->total() }} человек</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Дата регистрации</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($participants as $index => $participant)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $participant->name }}</td>
                                <td>{{ $participant->email }}</td>
                                <td>{{ $participant->pivot->registered_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('organizer.events.remove-attendee', [$event, $participant->id]) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Удалить участника?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-person-x"></i> Удалить
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="bi bi-people" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Пока нет зарегистрированных участников</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="row">
                <div class="col-12">
                    {{ $participants->links() ?? '' }}
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('organizer.events.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад к мероприятиям
        </a>
    </div>
</div>
@endsection
'@ | Out-File -FilePath resources\views\organizer\events\attendees.blade.php -Encoding UTF8
