@extends('header_menu')
@section('title')
    Запчасти 
@endsection
@section('main') 
@can('spm')
    <a href="/SparePart/{{$id}}/create" class="nav-link">Добавить</a>
@endcan
<a href="/SparePart/{{$id}}/doc_load" class="nav-link">Загрузить данные из калькуляции</a>

<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Наименование</th>
      <th scope="col">Кат. номер</th>
      <th scope="col">Заказанный номер</th>
      <th scope="col">Цена СК</th>
      <th scope="col">Цена СК с износом</th>
      <th scope="col">Цена оригинал</th>
      <th scope="col">Цена аналог</th>
      <th scope="col">Цена б/у</th>
      <th scope="col">Статус</th>
      <th scope="col">Дата заказа</th>
      <th scope="col">Ожидаемая дата получения</th>

    
    
      <th scope="col"></th>

    </tr>
     <tr>
      <th scope="col"></th>
      <th scope="col">Итого</th>
      <th scope="col"></th>
      <th scope="col"></th>
      <th scope="col">{{$summ_ic_full}}</th>
      <th scope="col">{{$summ_ic_half}}</th>
      <th scope="col">Сумма заказа</th>
      <th scope="col">{{$summ_order}}</th>
      <th scope="col"></th>
      <th scope="col"></th>
      <th scope="col"></th>
      <th scope="col"></th>
    
    
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
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->price_ic_full}}</th>
      <th scope="col"  onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->price_ic_half}}</th>
      <th scope="col" class="
      @if($sparepart->selected_mrk==1&&$sparepart->selected_price==1)zapmrk @endif
      @if($sparepart->selected_mrk==1)mrk @endif
      @if($sparepart->selected_price==1)zap @endif
      " onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->price_original}}</th>
      <th scope="col" class="
      @if($sparepart->selected_mrk==2&&$sparepart->selected_price==2)zapmrk @endif
      @if($sparepart->selected_mrk==2)mrk @endif
      @if($sparepart->selected_price==2)zap @endif
      " onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->price_analogue}}</th>
      <th scope="col" class="
      @if($sparepart->selected_mrk==3&&$sparepart->selected_price==3)zapmrk @endif
      @if($sparepart->selected_mrk==3)mrk @endif
      @if($sparepart->selected_price==3)zap @endif
      " onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->price_used}}</th>
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->status}}</th>
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->date_price}}</th>
      <th scope="col" onclick="window.location.href='/SparePart/{{$sparepart->id}}/edit'; return false">{{$sparepart->date_wait}}</th>
      <th scope="col"> <a onclick="if (!confirm('Точно удалить запчасть <{{$sparepart->name}}?>')) return false"  href="{{route('SparePart.destroy', $sparepart->id)}}" class="nav-link">Удалить</a>
      </th>
    </tr>
      
@endforeach
 </tbody>
</table>

 @endsection 