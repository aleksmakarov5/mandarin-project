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
       $body = $message->body_html;
       $body_start=mb_strpos($body,'<body');
       $body_end=mb_strpos($body,'</body>');
       if ($body_start){
       $body=mb_substr($body,$body_start,$body_end-$body_start);}
       echo $body;
    @endphp
     
       
    </p>
    </div>    
@endforeach
@endsection 