@extends('header_menu')
@section('title')
    Запчасти 
@endsection
@section('main') 
<a href="/SparePart/{{$id}}/create" class="nav-link">Добавить</a>
<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Наименование</th>
      <th scope="col">Кат. номер</th>
      <th scope="col">Заказанный номер</th>
      <th scope="col">Цена оригинал</th>
      <th scope="col">Цена аналог</th>
      <th scope="col">Цена б/у</th>
      <th scope="col">Дата заказа</th>
      <th scope="col">Статус</th>
    
    
      <th scope="col"></th>

    </tr>
  </thead>
  <tbody>

   
 
@foreach ($spareparts as $sparepart)
<tr >
    
      <th scope="col" >
        
        {{$sparepart->id}}
    
      </th> 
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->name}}</th>
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->part_number}} </th>
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->order_number}} </th>
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->price_original}}</th>
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->price_analogue}}</th>
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->price_used}}</th>
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->status}}</th>
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->date_price}}</th>
         <th scope="col"> <a href="{{route('SparePart.destroy', $sparepart->id)}}" class="nav-link">Удалить</a>
        </th>
    </tr>
      
@endforeach
 </tbody>
</table>

 @endsection 