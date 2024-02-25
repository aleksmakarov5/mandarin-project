@extends('layout')
@section('title')
    Загрузить документ
@endsection
@section('main_content')   
<h1>Загрузить документ</h1>
   <a href="/document/{{$id}}" class="nav-link">Отмена</a>
<br>
<form  action="/documents/{{$id}}/doc_add" method="post" enctype="multipart/form-data">
    @csrf              <!-- с версии Laravel 5.6 -->
    <input type="file" name="userfile">
    <input type="submit">
</form>
@endsection