@extends('layouts.admin')

@section('title', 'Admin index')

@section('content')

    <a href="{{ url()->previous() }}">← Назад</a>

<table>
    <caption>Users</caption>
<thead>
<tr>
    <th></th>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Stories</th>
    <th></th>
    <th></th>
    <th></th>
</tr>
</thead>
    <tbody>
    @foreach($users as $user)
    <tr>
        <td>
        @if($user->id === auth()->id())
            <b>You</b>
        @endif
        </td>
    <td>{{ $user->id }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>{{ $user->role }}</td>
    <td>{{ $user->stories_count }}</td>
    <td>
        @can('update', $user)
        <a href="{{ route('admin.users.edit-role', $user)  }}">Change Role</a>
        @endcan
    </td>
        <td>
            @if($user->is_blocked)
                @can('unblock', $user)
                <form method="POST" action="{{ route('admin.users.unblock', $user) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit">Разблокировать</button>
                </form>
                @endcan
            @else
                @can('block', $user)
                <form method="POST" action="{{ route('admin.users.block', $user) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit">Заблокировать</button>
                </form>
                @endcan
            @endif
        </td>
        @can('delete', $user)
        <td>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                @csrf
                @method('DELETE')

                <button type="submit">Удалить</button>
            </form>
        </td>
        @endcan
    </tr>
    @endforeach
    </tbody>
</table>

    {{ $users->links() }}

@endsection
