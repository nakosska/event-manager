@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2>🎉 Добро пожаловать в Event Manager</h2>
                    </div>
                    <div class="card-body">
                        <h3>Платформа для создания и продвижения мероприятий</h3>
                        <p class="lead">Создавайте мероприятия, регистрируйте участников, обсуждайте в комментариях</p>

                        @auth
                            <a href="{{ url('/events') }}" class="btn btn-primary btn-lg">Перейти к мероприятиям</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Войти</a>
                            <a href="{{ route('register') }}" class="btn btn-success btn-lg">Зарегистрироваться</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
