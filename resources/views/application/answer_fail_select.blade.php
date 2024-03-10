
@extends('header_menu')
@section('title')
    Причины отказа 
@endsection
@section('main') 
<form action="/answer_fail_save/{{$id}}" method="post">
    @csrf
@foreach ($fail_vars as $key=>$item)
<p>
<input type="checkbox" name="variant[]" id="var" value="{{$item->id}}">{{$item->variant}}</p>
    
@endforeach
<input class="nav-link" type="submit" value="Сохранить">
</form>
@endsection