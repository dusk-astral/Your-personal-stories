@extends('layouts.app')

@section('title', 'Edit story')

@section('content')

    <a href="{{ url()->previous() }}">← Назад</a>

    <h1>Редактирование истории: <span style="color:slategray">{{ $story->title }}</span></h1>

    <form method="POST" action="{{ route('stories.update', $story->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div>
            <label for="title"><b>Заголовок</b></label><br>

            <input id="title" type="text" name="title" value="{{ old('title', $story->title) }}" placeholder="Введите имя истории">

            <br>
            @error('title')
            <span style="color:red">*{{ $message }}</span>
            @enderror
        </div>
        <br>
        <div>
            <label for="body"><b>История</b></label><br>
            <textarea id="body" name="body" rows="8" cols="100" placeholder="Напишите вашу историю...">{{ old('body', $story->body) }}</textarea>

            <br>
            @error('body')
            <span style="color:red">*{{ $message }}</span>
            @enderror
        </div>
        <br>
        <div>
            <label for="tags"><b>Метки</b></label><br>
            <select name="tags[]" multiple>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" @selected(in_array($tag->id, old('tags', $story->tags->pluck('id')->all())))>
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
            <label for="image_path"><b>Изображение</b></label><br>
            <input id="image_path" name="image_path" type="file">

            @error('image_path')
            <span style="color:red">{{ $message }}</span>
            @enderror
        </div>
        <br>

        <button type="submit">Опубликовать</button>

    </form>

@endsection
