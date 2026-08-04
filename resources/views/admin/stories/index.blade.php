@use('App\Enums\StoryStatus')

@extends('layouts.admin')

@section('title', 'admin.stories')

@section('content')

    <a href="{{ url()->previous() }}">← Назад</a>

<h1>Истории</h1>

@auth
    <a href="{{ route('my-stories') }}">Мои истории</a>
@endauth

<style>
    .list {
        background: #eddfe7;
        padding: 20px;
        margin-bottom: 15px;
        border-radius: 25px;
        box-shadow: 0 3px 4px rgba(0,0,0,0.1);
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-item {
        display: flex;
        flex-direction: column;
    }

    .filters-wrapper {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
</style>

    <div class="filters-wrapper">
    <form method="GET" action="{{ route('admin.stories.index') }}">
        <div class="filter-row">
            <div class="filter-item">
            <label for="status">Статус</label>
            <select id="status" name="status">
                <option value="">Все</option>
                @foreach(StoryStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{$status->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-item">
            <label for="user_id">Автор</label>
            <select id="user_id" name="user_id">
                <option value="">Все</option>
                @foreach($users as $user)
                    <option value="{{$user->id}}" @selected(request('user_id') == $user->id)>{{$user->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-item">
            <label for="date_from">Дата с</label>
            <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}">
        </div>

        <div class="filter-item">
            <label for="date_to">Дата по</label>
            <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}">
        </div>

        <div class="filter-item">
            <label for="tags">Метки:</label>
                <select id="tags" name="tags[]" multiple size="1">
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" @selected(in_array($tag->id, request('tags',[]), true))>{{$tag->name}}</option>
                    @endforeach
                </select>
        </div>

        <button type="submit">Фильтровать</button>
        <a href="{{ route('admin.stories.index') }}" class="btn-secondary">Сбросить</a>
            </div>

        <div class="filters-wrapper">
            <label for="sort">Сортировать по:</label>
            <select id="sort" name="sort" size="1">
                <option value="latest" @selected(request('sort', 'latest') === 'latest')>Сначала новые</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Сначала старые</option>
                <option value="title_asc" @selected(request('sort') === 'title_asc')>Заголовок A-Z</option>
                <option value="title_desc" @selected(request('sort') === 'title_desc')>Заголовок Z-A</option>
                <option value="author_asc" @selected(request('sort') === 'author_asc')>Автор A-Z</option>
                <option value="author_desc" @selected(request('sort') === 'author_desc')>Автор Z-A</option>
            </select>
        </div>

    </form>
    </div>

<div>
    @forelse($stories as $story)
        <div class="list">
            Название: {{ $story->title }}<p>
                Статус: <b style="background: #e1c499; border-radius: 7px;">{{ $story->status }}</b><p>
                Дата создания: {{ $story->created_at->format('d.m.Y H:i') }}<p>

                <b>{{$story->id}} </b><a href="{{ route('admin.stories.show', $story->id) }}">Читать</a>

            @can('update', $story)
                <a href="{{ route('admin.stories.edit', $story) }}" style="display:inline-block;
                        padding:5px 4px; background:#a1d6b4; color:#fff; text-decoration:none;
                        border-radius:6px; position: absolute; right: 22px;"
                       onmouseover="this.style.backgroundColor='#8fbda0';"
                       onmouseout="this.style.backgroundColor='#a1d6b4';">
                Редактировать
                </a>
            @endcan

            @can('delete', $story)
                <form method="POST" action="{{ route('admin.stories.destroy', $story) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:darkred; position: absolute; right: 40px;">Удалить</button>
                </form>
            @endcan

            @can('approve', $story)
            <form method="POST" action="{{ route('admin.stories.approve', $story) }}">
                @csrf
                @method('PATCH')
                <button type="submit">Одобрить</button>
            </form>
            @endcan

            @can('reject', $story)
            <form method="POST" action="{{ route('admin.stories.reject', $story) }}">
                @csrf
                @method('PATCH')

                <label for="reason-{{$story->id}}">Причина отклонения:</label><br>
                <textarea name="reason" id="reason-{{$story->id}}" cols="50" rows="4" placeholder="Укажите причину отклонения истории">{{ old('reason') }}</textarea><br>

                <button type="submit">Отклонить</button>
            </form>
            @endcan

            <div>
                @if($story->user_id == auth()->id())
                    <b>вы:</b> <span style="background: #d7b2c7; border-radius: 7px;"> {{ $story->user->name }} </span>
                @else
                    <strong>автор:</strong> {{ $story->user->name }}
                @endif
            </div>

        </div>
    @empty
        @if($filtersApplied)
            <span style="color:red">
        По выбранным фильтрам ничего не найдено
        </span>
        <a href="{{ route('admin.stories.index') }}">Сбросить фильтры</a>
        @else
            Историй пока нет.
        @endif
    @endforelse
</div>

@auth
    <form method="POST" action="{{ route('logout') }}">
        <button type="submit" style="color:red; position: absolute; top: 10px; right: 20px;">Выйти из аккаунта</button>
    </form>
@endauth

{{ $stories->links() }}

@endsection
