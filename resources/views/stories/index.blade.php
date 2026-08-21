@use('App\Models\Story')
@use('App\Models\User')
@use('App\Enums\UserRole')

@extends('layouts.app')

@section('title', 'Stories')

@section('content')

    <h1>Истории</h1>

    @auth
        <p>
            Вы: <a href="{{ route('profile.show') }}">{{ auth()->user()->name }}</a><br>
            роль:
            @if($route = auth()->user()->roleRoute())
                <a href="{{$route}}">{{ucfirst(auth()->user()->role?->value)}}</a>
            @else
                {{ ucfirst(auth()->user()->role?->value) }}
            @endif
        </p>
    @endauth

    <p>
        @can('create', Story::class)
            <a href="{{ route('stories.create') }}">Создать историю</a>
        @endcan

        @auth
            <a href="{{ route('my-stories') }}">Мои истории</a>
        @endauth
    </p>

    <style>
        .stories {
            max-width: 900px;
            margin: 0 auto;
        }

        .story-card {
            background: #e9f8db;
            margin-bottom: 16px;
            padding: 16px;
            border-radius: 10px;
            box-shadow: 0 3px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }

        .story-card__content {
            flex: 1;
        }

        .story-card__image {
            width: 180px;
            flex-shrink: 0;
        }

        .story-card__image img {
            width: 100%;
            display: block;
            border-radius: 8px;
        }

        .story-actions {
            margin-top: 12px;
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
        <form method="GET" action="{{ route('stories.index') }}">
            <div class="filter-row">
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
                        <option value="{{ $tag->id }}" @selected(in_array($tag->id, old('tags', request('tags', [])),true))>{{$tag->name}}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit">Фильтровать</button>
                <a href="{{ route('stories.index') }}">Сбросить</a>
            </div>
            </div>

            <div class="filters-wrapper">
                <label for="sort">Сортировать по:</label>
                <select id="sort" name="sort">
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

    <div class="stories">
        @forelse($stories as $story)
            <div class="story-card">
                <div class="story-card__content">
                    <h2 style="margin: 0 0 8px 0;">{{ $story->title }}</h2>

                    <p style="margin: 0 0 8px 0;">
                        {{ Str::limit($story->body, 150) }}
                    </p>

                    <p style="margin: 0 0 8px 0;">
                        @if(auth()->check() && $story->user_id == auth()->id())
                            <strong>вы:</strong>
                            <a style="border-radius: 6px; background: #addaa4" href="{{ route('profile.show') }}">{{ $story->user->name }}</a>
                        @else
                            <strong>автор:</strong>
                            <a href="{{ route('profile.public', $story->user) }}">{{ $story->user->name }}</a>
                        @endif
                    </p>

                    <p style="margin: 0 0 8px 0;">
                        <strong>дата:</strong> {{ $story->created_at->format('d.m.Y H:i') }}<br>
                    </p>

                    <p>
                        <strong>метки:</strong>
                        @foreach($story->tags as $tag)
                            {{ $tag->name }}@if(!$loop->last), @endif
                        @endforeach
                    </p>

                    <p style="margin: 0;">
                        <a href="{{ route('stories.show', $story->id) }}">Читать</a>
                    </p>

                    @auth
                        <div class="story-actions">
                            @can('update', $story)
                                <a href="{{ route('stories.edit', $story) }}">Редактировать</a>
                            @endcan

                            @can('delete', $story)
                                <form method="POST" action="{{ route('stories.destroy', $story) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Удалить</button>
                                </form>
                            @endcan
                        </div>
                    @endauth
                </div>

                @if($story->image_path)
                    <div class="story-card__image">
                        <img src="{{ asset('storage/' . $story->image_path) }}" alt="{{ $story->title }}">
                    </div>
                @endif
            </div>

        @empty
            <div class="filters-wrapper">
            @if($filtersApplied)
                Нет историй с указанными фильтрами.
                <a href="{{ route('stories.index') }}">Сбросить фильтры</a>
            @else
                <span class="filters-wrapper">
                Историй пока нет
                </span>
            @endif
            </div>
        @endforelse
    </div>

    <div>
        {{ $stories->links() }}
    </div>

    @auth
        <form method="POST" action="{{ route('logout') }}">
            <button type="submit" style="color:red; position: absolute; top: 10px; right: 20px;">Выйти из аккаунта</button>
        </form>
    @endauth

    @guest
        <a href="{{ route('login.get') }}" style="display:inline-block;
padding:5px 4px; background:#a1d6b4; color:#fff; text-decoration:none;
border-radius:6px; position: absolute; top: 10px; right: 20px;"
           onmouseover="this.style.backgroundColor='#8fbda0';"
           onmouseout="this.style.backgroundColor='#a1d6b4';
">Войти в аккаунт</a>
    @endguest

@endsection
