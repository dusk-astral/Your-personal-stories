@extends('layouts.admin')

@section('title', 'Admin dashboard')

@section('content')

    <h1>Admin Dashboard</h1>

    <a href="{{ route('stories.index') }}">Истории</a>

    <a href="{{ route('admin.users.index') }}"><h2>Users</h2></a>

    <ul>
        <li>Total: {{ $usersCount['total'] }}</li>
        <li>Users: {{ $usersCount['user'] }}</li>
        <li>Moderators: {{ $usersCount['moderator'] }}</li>
        <li>Admins: {{ $usersCount['admin'] }}</li>
    </ul>

    <a href="{{ route('admin.stories.index') }}"><h2>Admin stories</h2></a>

    <ul>
        <li>Total: {{ $storyCount['total'] }}</li>
        <li>Pending: {{ $storyCount['pending'] }}</li>
        <li>Approved: {{ $storyCount['approved'] }}</li>
        <li>Rejected: {{ $storyCount['rejected'] }}</li>
    </ul>

    <h2>Latest 5th Users</h2>

    <ul>
        @foreach($latestUsers as $user)
            <li> <b>{{ $user->role->value }} - </b><a href="{{ route('profile.public', $user) }}">{{ $user->name }}</a></li>
        @endforeach
    </ul>

    <h2>Latest 5th stories</h2>

    <ul>
        @foreach($latestStories as $story)
            <li><b>{{ $story->status }}</b> - <a href="{{ route('stories.show', $story) }}">{{ $story->title }}</a> </li>
        @endforeach
    </ul>

@endsection
