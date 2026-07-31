@extends('layouts.admin')

@section('title', 'Admin dashboard')

@section('content')

    <h1>Admin Dashboard</h1>

    <a href="{{ route('admin.users.index') }}"><h2>Users</h2></a>

    <ul>
        <li>Total: {{ $usersCount['total'] }}</li>
        <li>Users: {{ $usersCount['user'] }}</li>
        <li>Moderators: {{ $usersCount['moderator'] }}</li>
        <li>Admins: {{ $usersCount['admin'] }}</li>
    </ul>

    <a href="{{ route('admin.stories.index') }}"><h2>Stories</h2></a>

    <ul>
        <li>Total: {{ $storyCount['total'] }}</li>
        <li>Pending: {{ $storyCount['pending'] }}</li>
        <li>Approved: {{ $storyCount['approved'] }}</li>
        <li>Rejected: {{ $storyCount['rejected'] }}</li>
    </ul>

    <h2>Latest 5th Users</h2>

    <ul>
        @foreach($latestUsers as $user)
            <li> <b>{{ $user->role->value }}</b> - {{ $user->name }}</li>
        @endforeach
    </ul>

    <h2>Latest 5th stories</h2>

    <ul>
        @foreach($latestStories as $story)
            <li><b>{{ $story->status }}</b> - {{ $story->title }} </li>
        @endforeach
    </ul>

@endsection
