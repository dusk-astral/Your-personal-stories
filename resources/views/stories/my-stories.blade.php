@use('App\Models\Story')
@use('App\Enums\StoryStatus')

@extends('layouts.app')

@section('title', 'my-stories')

@section('content')

    <a href="{{ url()->previous() }}">← Назад</a>

    <h1>Мои истории</h1>

    @can('create', Story::class)
        <a href="{{ route('stories.create') }}">Создать историю</a>
    @endcan

    <a href="{{ route('stories.index') }}">Истории</a>

    <style>
        .list {
            background: #d5e2eb;
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
        <form method="GET" action="{{ route('my-stories') }}">
            <div class="filter-row">
                <div class="filter-item">
                    <label for="date_from">Дата с:</label>
                    <input id="date_from" name="date_from" type="date" value="{{ request(('date_from')) }}">
                </div>

                <div class="filter-item">
                    <label for="date_to">Дата по:</label>
                    <input id="date_to" name="date_to" type="date" value="{{ request('date_to') }}">
                </div>

                <div class="filter-item">
                    <label for="status">Статус:</label>
                    <select id="status" name="status" size="1">
                        <option value="">Все</option>
                        @foreach(StoryStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label for="tags">Метки:</label>
                    <select id="tags" name="tags[]" multiple size="1">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" @selected(in_array($tag->id, request('tags', [])))>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit">Фильтровать</button>
                    <a href="{{ route('my-stories') }}">Сбросить фильтры</a>
                </div>
                </div>

            <div class="filters-wrapper">
                <label for="sort">Сортировать по:</label>
                <select id="sort" name="sort" size="1">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Сначала новые</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Сначала старые</option>
                    <option value="title_asc" @selected(request('sort') === 'title_asc')>Заголовок A-Z</option>
                    <option value="title_desc" @selected(request('sort') === 'title_desc')>Заголовок Z-A</option>
                </select>

            </div>
        </form>
    </div>

    @forelse($stories as $story)
        <div class="list">
            Название: {{ $story->title }}<p>
                Статус: <b>{{ $story->status }}</b><p>
                Дата создания: {{ $story->created_at->format('d.m.Y H:i') }}<p>
                <b>{{$story->id}} </b><a href="{{ route('stories.show', $story->id) }}">Читать</a>

            @can('update', $story)
                <a href="{{ route('stories.edit', $story) }}" style="display:inline-block;
                        padding:5px 4px; background:#b2cbdc; color:#fff; text-decoration:none;
                        border-radius:6px; position: absolute; right: 22px;"
                   onmouseover="this.style.backgroundColor='#82b3d3';"
                   onmouseout="this.style.backgroundColor='#b2cbdc';">
                    Редактировать
                </a>
            @endcan

            @can('delete', $story)
                <form method="POST" action="{{ route('stories.destroy', $story) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:darkred; position: absolute; right: 40px;">Удалить</button>
                </form>
            @endcan

            @if($story->user_id == auth()->id())
                <b>вы:</b> <span style="background: #b2cbdc; border-radius: 7px;">{{ $story->user->name }}</span>
            @else
                <strong>автор:</strong> {{ $story->user->name }}
            @endif
            <p>

            <p>
                <strong>Метки:</strong>
                @foreach($story->tags as $tag)
                    {{ $tag->name }}@if(!$loop->last),@endif
                @endforeach
            </p>

                {{--            @if($story->status === StoryStatus::Rejected)--}}
                {{--                @php--}}
                {{--                    $lastRejection = $story->moderations--}}
                {{--                ->where('action', StoryStatus::Rejected)--}}
                {{--                ->sortByDesc('created_at')--}}
                {{--                ->first();--}}
                {{--                @endphp--}}
                {{--                @if($lastRejection)--}}
                {{--                    <b>Причина отклонения:</b> {{$lastRejection->reason}}<br> Модератор: {{ $lastRejection->moderator->name }}--}}
                {{--                @endif--}}
                {{--            @endif--}}

                @if($story->status === StoryStatus::Rejected && $story->lastRejectionModeration)
                    <b>Причина отклонения:</b> {{$story->lastRejectionModeration->reason}}<br>
                    Модератор: {{ $story->lastRejectionModeration->moderator->name }}
            @endif

        </div>

    @empty
        <div class="filters-wrapper">
            @if($filtersApplied)
                Нет историй с указанными фильтрами.
                <a href="{{ route('my-stories') }}">Сбросить фильтры</a>
            @else
                <p>У вас пока нет историй.</p>
            @endif
        </div>
    @endforelse

    @auth
        <form method="POST" action="{{ route('logout') }}">
            <button type="submit" style="color:red; position: absolute; top: 10px; right: 20px;">Выйти из аккаунта</button>
        </form>
    @endauth

    {{$stories->links()}}

@endsection
