@extends('header_menu')
@section('title')
  Работы 
@endsection
@section('main') 
<a href="/Work/{{$id}}/doc_load" class="nav-link">Загрузить данные из калькуляции</a>
<br>
<a href="/Work/{{$id}}/create" class="nav-link">Добавить</a>

<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Наименование</th>
     
      <th scope="col">Сумма</th>
      <th scope="col">Цена сервиса</th>
      <th scope="col"></th>

    </tr>
      <tr>
      <th scope="col"></th>
      <th scope="col">Итого</th>
      <th scope="col">{{$summ_ic}}</th>
      <th scope="col">{{$summ_service}}</th>
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
      <th scope="col" onclick="window.location.href='/Work/{{$Work->id}}/edit'; return false">{{$Work->summ}}</th>
      <th scope="col" onclick="window.location.href='/Work/{{$Work->id}}/edit'; return false">{{$Work->price_service}}</th>
         <th scope="col"> <a onclick="if (!confirm('Точно удалить работу <{{$Work->name}}?>')) return false"  href="{{route('Work.destroy', $Work->id)}}" class="nav-link"><img src="/storage/img/delete.png" width="30" alt="delete"></a>
        </th>
    </tr>
      
@endforeach
 </tbody>
</table>

 @endsection 