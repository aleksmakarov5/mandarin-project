@extends('layout')
@section('main_content')
    <a href="/application/{{ $application->id }}" class="nav-link">Отмена</a>
    <form action="/applications/{{ $application->id }}/update" method="POST">
        @csrf
        <input type="submit" value="Обновить" class="nav-link">
        @include('application.form')


    </form>
@endsection
