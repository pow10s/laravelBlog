<!--create.blade.php -->
@extends('layouts.app')
@section('content')
    <div class="container">
        <form class="form-horizontal" method="POST" action='/store'>
            <label class="control-label">Title</label>
            <input type="text" class="form-control" name="title">
            <label class="control-label">Content</label>
            <textarea name ="content" class="form-control"></textarea>
            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
            <input class="btn btn-primary" type="submit" value="Create">
        </form>
    </div>
@endsection