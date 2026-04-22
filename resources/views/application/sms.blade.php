@extends('header_menu')
@section('title')
    Заявкa
@endsection
@section('main')
    <center>
        <file-upload :id_app="{{ $id }}" :status_id="35" :load_name="'Скриншот СМС'"
            :load_location="'Скриншот_СМС'" />
        <div>
            {{ $text }}
        </div>
        <a class="nav-link" href="{{ $href }}">---Перейти к отправке---</a>
    </center>
@endsection
