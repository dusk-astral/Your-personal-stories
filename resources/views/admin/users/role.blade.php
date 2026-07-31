@extends('layouts.admin')

@section('title', 'User role')

@section('content')

    <h1>Change Role: {{ $user->name }}</h1>

    <form method="POST" action="{{ route('admin.users.update-role', $user) }}">
        @csrf
        @method('PATCH')

            <label for="role">Role</label>
        <select name="role" id="role">
            @foreach($roles as $role)
                <option value="{{$role->value}}" @selected($user->role === $role)>
                    {{ $role->value }}
                </option>
            @endforeach
        </select>

        <button type="submit">Save</button>

    </form>

    @can('delete', $user)
    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
        @csrf
        @method('DELETE')

        <button type="submit">Удалить пользователя</button>
    </form>
    @endcan

@endsection
