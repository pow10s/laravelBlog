<!-- articles.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <ul>
            @foreach($articles as $article)
                <li><a href="/show/{{$article->id}}">{{$article->title}}</a></li>
            @endforeach
        </ul>
        @can('create') <!-- ��������� ����� -->
        <a href="/create">Add article</a>
        @endcan
    </div>

@endsection