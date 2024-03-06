@extends('header_menu')
@section('title')
    Запчасти 
@endsection
@section('main') 

<header class="d-flex justify-content-left py-3">
      <ul class="nav nav-pills">
        <li class="nav-item"><a href="/SparePart/{{$id}}/doc_load" class="nav-link">Загрузить данные из калькуляции</a></li>
        @can('spm')
         <li class="nav-item"><a href="/SparePart/{{$id}}/edit" class="nav-link">Редактировать</a></li>
        @endcan
        <li class="nav-item"><a href="/SparePart/{{$id}}/price_select" class="nav-link">Выбрать запчасти</a></li>
          </ul>
</header>

</div>

    
<center>

<table id="demo" class="editable">
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
    <form action="/SpareParts/store" name="callback" method="POST">
     @csrf
     @error('name')
          <p class="text-red-500">{{$message}}</p>
      @enderror
      
<input name = "id_application" type="hidden" value="{{$id}}">
<input name = "author" type="hidden" value="{{Auth::user()->GroupId}}">
<td></td>
<td>
   <input type="text" name="name" id="name"  class="form-control"  >
</td>
<td>

 <input type="text" name="part_number" id="part_number"  class="form-control"  >

</td><td>

 <input type="text" name="order_number" id="order_number"  class="form-control"  >
</td><td>

 <input type="text" name="price_ic_full" id="price_ic_full"  class="form-control"  >
</td><td>

 <input type="text" name="price_ic_half" id="price_ic_half"  class="form-control"  >
   
</td><td>

 <input type="text" name="price_original" id="price_original"  class="form-control"  >

</td><td>
 <input type="text" name="price_analogue" id="price_analogue"  class="form-control"  >
     
</td><td>
 <input type="text" name="price_used" id="price_used"  class="form-control"  >

</td><td>
<input type="text" name="status" id="status"  class="form-control"  >

</td><td>
<input type="date" name="date_price" id="date_price"  class="form-control"  >
</td><td>
<input type="date" name="date_wait" id="date_wait"  class="form-control"  >

</td><td>
   <img src="/storage/img/edit.png" width="30" alt="edit" onclick="document.forms['callback'].submit();">
</td>
</form>
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
<tr @if ( $sparepart->author==5)
  class="pink"
@endif>
    
      <td scope="col" >
        
        {{$sparepart->id}}
    
      </td> 
      <td scope="col">{{$sparepart->name}}</td>
      <td scope="col">{{$sparepart->part_number}} </td>
      <td scope="col">{{$sparepart->order_number}} </td>
      <td scope="col">{{$sparepart->price_ic_full}}</td>
      <td scope="col" >{{$sparepart->price_ic_half}}</td>
      <td scope="col" class="
      @if($sparepart->selected_mrk==1&&$sparepart->selected_price==1)zapmrk @endif
      @if($sparepart->selected_mrk==1)mrk @endif
      @if($sparepart->selected_price==1)zap @endif
      ">{{$sparepart->price_original}}</td>
      <td scope="col" class="
      @if($sparepart->selected_mrk==2&&$sparepart->selected_price==2)zapmrk @endif
      @if($sparepart->selected_mrk==2)mrk @endif
      @if($sparepart->selected_price==2)zap @endif
      ">{{$sparepart->price_analogue}}</td>
      <td scope="col" class="
      @if($sparepart->selected_mrk==3&&$sparepart->selected_price==3)zapmrk @endif
      @if($sparepart->selected_mrk==3)mrk @endif
      @if($sparepart->selected_price==3)zap @endif
      ">{{$sparepart->price_used}}</td>
      <td scope="col">{{$sparepart->status}}</td>
      <td scope="col">{{$sparepart->date_price}}</td>
      <td scope="col">{{$sparepart->date_wait}}</td>
      <td scope="col"> <a onclick="if (!confirm('Точно удалить запчасть <{{$sparepart->name}}?>')) return false"  href="{{route('SparePart.destroy', $sparepart->id)}}" class="nav-link"><img src="/storage/img/delete.png" width="30" alt="delete"></a>
      </td>
    </tr>
      
@endforeach

 </tbody>
</table>
</center>
<div class="container">
 @endsection 