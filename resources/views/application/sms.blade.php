@extends('layout')
@section('title')
    Отправка смс по {{ $id }}
@endsection
@section('main_content')
    <center>

        <div>
            {{ $text }}
        </div>
        <a class="nav-link" href="{{ $href }}">---Перейти к отправке---</a>

        Загрузить скриншот <file-upload :id_app="{{ $id }}" :status_id="35" :load_name="'Скриншот СМС'"
            :load_location="'Скриншот_СМС'" />
    </center>
@endsection
