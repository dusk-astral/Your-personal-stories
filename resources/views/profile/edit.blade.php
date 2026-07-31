@extends('layouts.app')

@section('title', 'edit-profile')

@section('content')

    <a href="{{ route('profile.show') }}">←Back</a>

    <h1>Редактировать профиль</h1>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div>
        <label for="name">Имя:</label>
        <input type="text" name="name" id="name" placeholder="Введите новое имя" value="{{ old('name', $user->name) }}" required="required">
        @error('name') {{ $message }}  @enderror
        </div>

        <div>
            <label>
                Бография:
                <textarea name="bio" rows="5" >{{ old('bio', $user->bio) }}</textarea>
            </label>
            @error('bio') {{ $message }} @enderror
        </div>

        <div>
            <button type="submit">Сохранить</button>
        </div>

        <div>
        <label for="avatar">Аватар</label>
        <input id="avatar" name="avatar_path" type="file">
        <span style="color:red">@error('avatar_path') {{ $message }}  @enderror</span>
        <button type="submit">Загрузить</button>
        </div>

    </form>

@endsection
