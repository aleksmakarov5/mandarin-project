@extends('layout')
@section('title')
    Зарплатная ведомость
@endsection
@section('main_content')
    <div id="app">
        @php
            // dd($attachments);
        @endphp
        <zp-ved-buh />
    </div>
@endsection
