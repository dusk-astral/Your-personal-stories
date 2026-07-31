@extends('layouts.app')

@section('title', 'Create history')

@section('content')

    <a href="{{ url()->previous() }}">← Назад</a>

    <h1>Создать историю</h1>

{{--    @if ($errors->any())--}}
{{--        <div>--}}
{{--            <ul>--}}
{{--                @foreach($errors->all() as $error)--}}
{{--                    <li style="color:red">{{ $error }}</li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        </div>--}}
{{--    @endif--}}

<div>
    <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="title"><b>Заголовок</b></label><br>
        <input id="title" type="text" name="title" value="{{ old('title') }}" placeholder="Введите имя истории">

            <br>
            @error('title')
            <span style="color:red">*{{ $message }}</span>
            @enderror
        </div>
        <br>
        <div>
            <label for="body"><b>История</b></label><br>
            <textarea id="body" name="body" rows="8" cols="100" placeholder="Напишите вашу историю...">{{ old('body') }}</textarea>

            <br>
            @error('body')
            <span style="color:red">*{{ $message }}</span>
            @enderror
        </div>
        <br>
        <div>
            <label for="tags"><b>Метки</b></label><br>
            <select id="tags" name="tags[]" multiple>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" @selected (in_array($tag->id, old('tags', [])))>
                    {{ $tag->name }}
                    </option>
                @endforeach
            </select>

            <br>
            @error('tags')
            <span style="color:red">*{{ $message }}</span>
            @enderror
        </div>
        <br>

        <div>
            <label for="image"><b>Изображение</b></label><br>
            <input id="image" type="file" name="image_path">
        </div>
        <br>
        <button type="submit">Опубликовать</button>
    </form>
</div>
@endsection

