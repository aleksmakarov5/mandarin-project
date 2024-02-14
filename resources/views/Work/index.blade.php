@extends('header_menu')
@section('title')
  Работы 
@endsection
@section('main') 
<a href="/Work/{{$id}}/create" class="nav-link">Добавить</a>
<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Наименование</th>
      <th scope="col">Цена</th>
      <th scope="col">Количество нормочасов</th>
      <th scope="col">Сумма</th>
      <th scope="col"></th>

    </tr>
  </thead>
  <tbody>

   
 
@foreach ($Works as $Work)
<tr >
    
      <th scope="col" >
        
        {{$Work->id}}
    
      </th> 
      <th scope="col" onclick="window.location.href='/Work/{{$Work->id}}/edit'; return false">{{$Work->name}}</th>
      <th scope="col" onclick="window.location.href='/Work/{{$Work->id}}/edit'; return false">{{$Work->price}} </th>
      <th scope="col" onclick="window.location.href='/Work/{{$Work->id}}/edit'; return false">{{$Work->count_hours}} </th>
      <th scope="col" onclick="window.location.href='/Work/{{$Work->id}}/edit'; return false">{{$Work->summ}}</th>
         <th scope="col"> <a href="{{route('Work.destroy', $Work->id)}}" class="nav-link">Удалить</a>
        </th>
    </tr>
      
@endforeach
 </tbody>
</table>

 @endsection 