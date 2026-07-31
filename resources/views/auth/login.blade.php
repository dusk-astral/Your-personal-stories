@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <h1>Вход</h1>

    <div style="color:red">
    @error('email')
    {{$message}}
    @enderror
    <br>
    </div>

    <div>
    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        Email пользователя:<input type="email" name="email" placeholder="Email" value="{{ old('email') }}"><br>
        Пароль:<input type="password" name="password" placeholder="Пароль"><p>

        <label for="remember_me">Запомнить меня</label>
        <input id="remember_me" name="remember" type="checkbox" checked="checked">

        <button type="submit">Войти</button>

    </form>

    <a href="{{ route('register.get') }}">Отсутсвтует аккаунт?(Зарегистрироваться)</a>
</div>
@endsection
