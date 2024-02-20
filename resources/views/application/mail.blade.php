@extends('header_menu')
@section('title')
    Заявкa 
@endsection
@section('main') 
@foreach ($messages as $message)
<div class="alert alert-warning">
    <h3>
        {{ $message->subject }}
        </h3>
    <p>
       
        {{ $message->body_html }}
    </p>
    </div>    
@endforeach
 @endsection 