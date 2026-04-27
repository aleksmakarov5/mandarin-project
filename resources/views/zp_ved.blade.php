@extends('layout')
@section('title')
    Зарплатная ведомость
@endsection
@section('main_content')
    <div id="app">
        @php
            // dd($attachments);
        @endphp
        <zp-ved-buh :user_id="{{ json_encode(Auth::user()->id) }}" :gate="{{ json_encode($gate) }}" />
    </div>
@endsection
