<!-- show.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{$article->title}}</h1>
        {{$article->content}}
    </div>
@endsection