<!-- show.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{$article->title}}</h1>

        <p>{{$article->content}}</p>
        @can('edit')
        <a href="/edit/{{$article->id}}">Edit</a>
        @endcan
    </div>

@endsection