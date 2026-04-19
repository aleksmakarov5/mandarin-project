@extends('header_menu')
@section('title')
    Заявкa
@endsection
@section('main')
    <center>
        <div>
            {{ $text }}
        </div>
        <a class="nav-link" href="{{ $href }}">---Перейти к отправке---</a>
    </center>
@endsection
