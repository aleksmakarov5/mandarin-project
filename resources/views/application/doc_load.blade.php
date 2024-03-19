@extends('layout')
@section('main_content')
    <a href="/application/{{ $id }}" class="nav-link">Отмена</a>
    <h1>Выберите документ</h1>
    <form action="data_load" method="POST">
        @csrf
        <div class="form-float">

            <select class="form-cont" id="location" name="location">
                @foreach ($documents as $item)
                    <option value="{{ $item->location }}">{{ $item->file_name }}</option>
                @endforeach
            </select>

        </div>
        <button class="nav-link" type="submit">Выбрать</button>
    </form>
@endsection
