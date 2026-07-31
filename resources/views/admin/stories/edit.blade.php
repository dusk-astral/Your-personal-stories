@extends('layouts.admin')

@section('title', 'admin.story')

@section('content')

    <h1>Редактирование истории: {{ $story->title }}</h1>

    <form method="POST" action="{{ route('admin.stories.update', $story) }}">
        @csrf
        @method('PATCH')

        <div>
        <label for="title"><b>Заголовок</b></label><br>
        <input id="title" name="title" type="text" value="{{ old('title', $story->title) }}" placeholder="Введите заголовок">

        <br>
        @error('title')
        <span style="color:red">*{{ $message }}</span>
        @enderror
        </div><br>

        <div>
            <label for="body"><b>История</b></label><br>статус истории: {{ $story->status }}<br>
            <textarea id="body" name="body" rows="8" cols="100" placeholder="Напишите вашу истори...">{{ old('body', $story->body) }}</textarea>

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
        <button type="submit">Опубликовать</button>

    </form>

@endsection
