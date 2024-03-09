@extends('layout')
@section('title')
    Почта
@endsection
@section('main_content')   
<h1>Все письма</h1> 

@foreach ($messages as $message)
<div class="alert alert-warning">
    <h3>
        @php
    $ret = "";
    //перебираем элементы
    for ($i=0; $i<count($elements); $i++) {
        $charset = $elements[$i]->charset;//кодировка
        $text = $elements[$i]->text;//закодированный текст
        if($charset == 'default'){
            //если элемент не кодирован, то значение кодировки default
            $ret .= $text;
        }else{
            //приводим всё кодировке к UTF-8
            $ret .= iconv($charset,"UTF-8",$text);
        }
    }
    echo $ret;
        @endphp
      
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