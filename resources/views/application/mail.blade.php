@extends('header_menu')
@section('title')
    Заявкa 
@endsection
@section('main') 
@foreach ($messages as $message)
<div class="alert" role="alert" style="width: 100%; background: rgb(116, 116, 255);">
   <div class="card-body">
    <h5 class="card-title"> {{ $message->subject }}</h5>
    <p class="card-text">
        @php
       $body = $message->body_html;
       $body_start=mb_strpos($body,'<body');
       $body_end=mb_strpos($body,'</body>');
       if($body_start)
       $body=mb_substr($body,$body_start,$body_end-$body_start);
       echo $body;
    @endphp
    </p>
     </div>
</div>
   
@endforeach
 @endsection 