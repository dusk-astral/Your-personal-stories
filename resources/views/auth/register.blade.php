@extends('layouts.guest')

@section('title', 'Registration')

@section('content')
    <h1>Регистрация</h1>
<div>
    <form method="POST" action="{{ route('register.post') }}">
        @csrf

        Имя пользователя:<input type="text" name="name" placeholder="Name" value="{{ old('name') }}"><br>
        Email пользователя:<input type="email" name="email" placeholder="Email" value="{{ old('email') }}"><br>
        Пароль:<input type="password" name="password" placeholder="Пароль"><br>
        Подтверждение пароля:<input type="password" name="password_confirmation" placeholder="Повторите пароль"><p>

            <label for="remember_me">Запомнить меня</label>
            <input id="remember_me" name="remember" type="checkbox" checked="checked">

            <button type="submit">Зарегестрироваться</button> <a href="{{ route('login.get') }}">Войти</a>

    </form>
</div>
@endsection
