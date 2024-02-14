@extends('layout')
@section('title')
    Почта
@endsection
@section('main_content')   
<h1>Все письма</h1> 

@foreach ($messages as $message)
<div class="alert alert-warning">
    <h3>
        {{ $message->subject }}
        </h3>
    <p>
        @php
        echo htmlspecialchars_decode($message->body_html);
        @endphp
    </p>
    </div>    
@endforeach
@endsection 