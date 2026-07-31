@extends('layouts.app')
@use('App\Enums\StoryStatus')

@section('title', 'My-profile')

@section('content')

    <h1>Мой профиль</h1>

    @if($user->avatar_path)
        <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="Avatar" width="150" height="150">
    @else
        <div style="
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #87d3ff;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        text-transform: uppercase;">
            {{ mb_substr($user->name, 0, 1) }}
        </div>
    @endif

    <p><strong>Имя:</strong> {{ $user->name }}</p>

    @if($user->bio)
        <p><strong>Биография:</strong> {{ $user->bio }}</p>
    @endif

    <p><strong>Истории:</strong> {{$user->storiesCount()}}</p>

    <p><strong>Опубликованно:</strong> {{$user->approvedStoriesCount()}}</p>

    <p><strong>Email:</strong> {{ $user->email }}</p>

    <p><strong>Дата регистрации:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>

    <p><strong>Статус:</strong> {{ $user->isBlocked() ? 'Заблокирован' : 'Активен' }}</p>

    <p><a href="{{ route('profile.edit') }}">Редактировать профиль</a></p>

    <h2><a href="{{ route('my-stories') }}">Мои истории</a></h2>

    @forelse($stories as $story)

        <div>
            <h3>
                <a href="{{ route('stories.show', $story) }}">{{ $story->title }}</a>
            </h3>

            <p>
                {{ Str::limit($story->body, 150) }}
            </p>

            <p>
                Статус:
                @if($story->status === StoryStatus::Approved)
                Опубликовано
                @elseif($story->status === StoryStatus::Rejected)
                Отклонено
                @else
                На модерации
                @endif
            </p>

            <p><small>Дата: {{ $story->created_at->format('d.m.Y') }}</small></p>
        </div>
    @empty
        У вас пока нет историй.
    @endforelse

    @if($stories->hasPages())
        {{$stories->links()}}
    @endif

@endsection
