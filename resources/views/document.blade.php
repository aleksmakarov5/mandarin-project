@extends('header_menu')
@section('title')
  Документы 
@endsection
@section('main') 
<a href="/applications/create" class="nav-link">Добавить документ</a>
<div class="row">
 @foreach ($document as $doc)
 
    {{-- <a href="/showdoc/{{$doc->location}}" class="nav-link">!{{$doc->file_name}}</a> --}}
{{--    
 <a href="/storage/files/{{$doc->location}}" class="nav-link" target="_blank">{{$doc->file_name}}</a> --}}
 
 <div class="card" style="width: 20rem;">
  <div class="row g-0">
     <div class="col-md-4">
   <a href="/storage/files/{{$doc->location}}"  target="_blank">
   
      
  <img src="/storage/img/{{mb_substr($doc->mime_type,mb_strpos($doc->mime_type,'/'),strlen($doc->mime_type)-mb_strpos($doc->mime_type,'/'))}}.png" class="card-img-top" alt="{{mb_substr($doc->mime_type,mb_strpos($doc->mime_type,'/'),strlen($doc->mime_type)-mb_strpos($doc->mime_type,'/'))}}">
  </a> 
</div>
 
   <div class="col-md-8">
    <a href="{{$id}}/doc_edit/{{$doc->id}}" ><img src="/storage/img/edit.png" width="35" alt="edit">
    </a>
    <a href="/doc_delete/{{$doc->id}}" ><img src="/storage/img/delete.png" width="35" alt="delete">
    </a>
  <div class="card-body">
       <h5 class="card-title">{{$doc->file_name}}</h5>  
  </div>
  
  </div>
  </div> 
  
</div>

 @endforeach
 </div>
 @endsection