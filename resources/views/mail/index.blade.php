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
        
     {{$message->body_html
     }}
       
    </p>
    </div>    
@endforeach
@endsection 