@extends('layout')
@section('main_content')
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            </ul>
        </div>
    @endif
    <a href="/applications/{{ $id }}/edit" class="nav-link">Отмена</a>
    <form action="/lawyers/store/{{ $id }}" method="POST">
        @csrf



        <div class="form-float">
            <input type="text" name="lawyer_name" id="lawyer_name" class="form-cont">
            <label for="lawyer_name">ФИО</label>
        </div>

        <div class='lbl'>
            <label for="color">Готов сотрудничать</label>
        </div>
        <div class="form-float">

            <select class="form-cont" id="color" name="color">
                <option value="1" selected>Да</option>
                <option value="0">Нет</option>
            </select>

        </div>
        <input type="submit" value="Создать" class="nav-link">

    </form>
@endsection
