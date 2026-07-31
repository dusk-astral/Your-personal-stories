@extends('layouts.admin')

@section('title', 'admin show')

@section('content')

    <a href="{{ url()->previous() }}">← Назад</a>

    <div style="text-align: center;">
        @if($story->image_path)
            <img src="{{ asset('storage/' . $story->image_path) }}" alt="Story_image" width="500" height="300">
        @endif
    </div>

    <ul>
        <li>ID: <b>{{$story->id}}</b></li><p>
            <li>Title: {{$story->title}}</li><p>
            <li>Status: {{$story->status}}</li><p>
            <li>Created_at: {{$story->created_at}}</li><p>
            <li>Description: <br><span style="background: #e0e0e0">{{$story->body}}</span></li><p>
    </ul>

    @foreach($story->tags as $tag)
        <span style="
        background: #ff69b4;
        border-radius: 5px;
        box-shadow: 0 4px 8px 2px
       rgba(142, 163, 153, 1);">
                <span style="color:navajowhite">{{ $tag->name }}</span></span> @if(!$loop->last), @endif
    @endforeach

@endsection
