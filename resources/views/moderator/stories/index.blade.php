@extends ('layouts.app')

@section ('title', 'stories moderator')

@section('content')

    <h1>Страница модератора: истории</h1>

    <a href="{{ route('my-stories') }}">Мои истории</a>

    <a href="{{ route('stories.index') }}">Истории</a>

    <style>
        .list {
            background: #c6bedf;
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
    <form method="GET" action="{{ route('moderator.stories.index') }}">
        <div class="filter-row">
            <div class="filter-item">
            <label for="user_id">Автор:</label>
            <select id="user_id" name="user_id">
                <option value="">Все</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{$user->name}}</option>
                @endforeach
            </select>
            </div>

            <div class="filter-item">
            <label for="date_from">Дата с:</label>
            <input id="date_from" name="date_from" type="date" value="{{ request('date_from') }}">
            </div>

            <div class="filter-item">
                <label for="date_to">Дата по:</label>
                <input id="date_to" name="date_to" type="date" value="{{ request('date_to') }}">
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
                <a href="{{ route('moderator.stories.index') }}">Сбросить</a>
            </div>
        </div>

        <div class="filters-wrapper">
            <label for="sort">Сортировать по:</label>
            <select id="sort" name="sort" size="1">
                <option value="latest" @selected(request('sort', 'latest') === 'latest')>Сначала новые</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Сначала старые</option>
                <option value="title_asc" @selected(request('sort') === 'title_asc')>Заголовок A-Z</option>
                <option value="title_desc" @selected(request('sort') === 'title_desc')>Заголовок Z-A</option>
                <option value="author_asc" @selected(request('sort') === 'author_asc')>Автор A-Z</option>
                <option value="author_desc  " @selected(request('sort') === 'author_desc')>Автор Z-A</option>
            </select>
        </div>
    </form>
    </div>

    <div>
            @forelse($stories as $story)
                <div class="list">
                    Название: {{ $story->title }}<p>
                        Статус: <b style="background: #fab2d4; border-radius: 7px;">{{ $story->status }}</b><p>
                        Дата создания: {{ $story->created_at->format('d.m.Y H:i') }}<p>
                        <b>{{$story->id}} </b><a href="{{ route('moderator.stories.show', $story) }}">Читать</a>

                    @can('approve', $story)
                        <form method="POST" action="{{ route('moderator.stories.approve', $story) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit">Одобрить</button>
                        </form>
                    @endcan

                    @can('reject', $story)
                    <form method="POST" action="{{ route('moderator.stories.reject', $story) }}">
                        @csrf
                        @method('PATCH')

                        <label for="reason-{{$story->id}}">Причина отклонения:</label><br>
                        <textarea name="reason" id="reason-{{$story->id}}" rows="4" cols="50" placeholder="Напишите причину отклонения истории">{{ old('reason') }}</textarea><br>

                        @error('reason')
                        {{ $message }}
                        @enderror

                        <button type="submit">Отклонить</button>
                    </form>
                    @endcan

                    <div>
                        @if($story->user_id == auth()->id())
                            <b>вы:</b><span style="background: #a090d0; border-radius: 7px;"> {{ $story->user->name }}</span>
                        @else
                            <strong>автор:</strong> {{ $story->user->name }}
                        @endif
                    </div>

                </div>
        @empty
                <div class="filters-wrapper">
                @if($filtersApplied)
                    Нет историй с указанными фильтрами.
                    <a href="{{ route('moderator.stories.index') }}">Сбросить фильтры</a>
                @else
                    Историй пока нет.
               @endif
                </div>
            @endforelse
    </div>

        <form method="POST" action="{{ route('logout') }}">
            <button type="submit" style="color:red; position: absolute; top: 10px; right: 20px;">Выйти из аккаунта</button>
        </form>

    {{ $stories->links() }}

@endsection
