@extends('layout')
@section('title')
    Реализации
@endsection
@section('main_content')
    <div id="app">
        @php
            // dd($attachments);
        @endphp
        <realiz :attachments="{{ json_encode($attachments) }}" />

    </div>
@endsection
