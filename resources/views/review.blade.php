@extends('layout')
@section('title')
    Отзывы
@endsection
@section('main_content')   
<h1>Форма добавления отзыва</h1> 
@if($errors->any())



<div class="alert alert-danger">
    <ul>
    
       @foreach ($errors->all() as $item)
           <li>{{$item}}</li>
       @endforeach
       
    </ul>
</div>
@endif
<form action="/review/check" method="POST">
    @csrf
<input type="email" name="email" id="email" placeholder="Введите email" class="form-control"><br>
<input type="text" name="subject" id="subject" placeholder="Введите отзыв" class="form-control"><br>
<textarea name="message" id="message" cols="30" rows="10" class="form-control"></textarea><br>
<button type="submit" class="btn btn-success">Отправить</button>
</form>
<h1>Все Отзывы</h1>
@foreach ($reviews->all() as $element)
<div class="alert alert-warning">
    <h3>
        {{ $element->subject }}
        </h3>
    <b>{{ $element->email }}</b>
    <p>
        {{ $element->message }}
    </p>
    </div>    
@endforeach
@endsection 