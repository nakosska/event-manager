@'
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="bi bi-tags"></i> Категории мероприятий</h1>
        </div>
        @can('access-admin-panel')
        <div class="col-md-4 text-end">
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Создать категорию
            </a>
        </div>
        @endcan
    </div>

    <div class="row">
        @forelse($categories ?? [] as $category)
            <div class="col-md-3 mb-4">
                <div class="card category-card">
                    <div class="card-body text-center">
                        <div class="category-icon mb-3">
                            <i class="bi bi-folder" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <p class="card-text text-muted small">
                            {{ $category->events_count ?? 0 }} мероприятий
                        </p>
                        <a href="{{ route('events.index', ['category' => $category->id]) }}" 
                           class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye"></i> Смотреть
                        </a>
                        @can('access-admin-panel')
                            <div class="mt-2">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Удалить категорию?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> Категорий пока нет
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
'@ | Out-File -FilePath resources\views\categories\index.blade.php -Encoding UTF8
