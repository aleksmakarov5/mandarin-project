@extends('header_menu')
@section('title')
  Документы 
@endsection
@section('main') 

 @foreach ($document as $doc)
 
    <a href="/showdoc/{{$doc->location}}" class="nav-link">{{$doc->file_name}}</a>
 
 @endforeach
 @endsection