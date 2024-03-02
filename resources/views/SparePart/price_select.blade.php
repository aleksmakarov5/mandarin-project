@extends('header_menu')
@section('title')
    Запчасти 
@endsection
@section('main') 
</div>


<a href="/SparePart/{{$id}}" class="nav-link">Отмена</a>
<form action="/SparePart/{{$id}}/update_price" method="POST">
@csrf

      <input type="hidden" name="id_application"  value='{{$id}}'>

<table class="editable">
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
      <th scope="col">Ремонт</th>
      <th scope="col">Статус</th>
      <th scope="col">Дата заказа</th>
      <th scope="col">Ожидаемая дата получения</th>

    
    
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>

   
 
@foreach ($spareparts as $key=>$sparepart)
<tr  @if ( $sparepart->author==5)
  class="pink"
@endif >
    
      <td scope="col" >
        
        {{$key}}
    
      </td> 
      <input type="hidden" name="id[{{$key}}]"  id='id{{$key}}' value='{{$sparepart->id}}'>
   
      <td scope="col">{{$sparepart->name}}</td>
      <td scope="col">{{$sparepart->part_number}} </td>
      <td scope="col">{{$sparepart->order_number}} </td>
      <td scope="col">{{$sparepart->price_ic_full}}</td>
      <td scope="col" >{{$sparepart->price_ic_half}}</td>
      @if ((Auth::user()->GroupId==6))
          
      
      <td scope="col" class="
      @if($sparepart->selected_mrk==1&&$sparepart->selected_price==1)zapmrk @endif
      @if($sparepart->selected_mrk==1)mrk @endif
      @if($sparepart->selected_price==1)zap @endif
      ">
      <div class="form_radio_btn zap">
	<input id="radio-1{{$key}}" type="radio" name="selected_price[{{$key}}]" value="1" @if ($sparepart->selected_price == 1)
         checked
     @endif>
	<label for="radio-1{{$key}}">&nbsp{{$sparepart->price_original}}</label>
</div>
      
    </td>
      <td scope="col" class="
      @if($sparepart->selected_mrk==2&&$sparepart->selected_price==2)zapmrk @endif
      @if($sparepart->selected_mrk==2)mrk @endif
      @if($sparepart->selected_price==2)zap @endif
      ">
      <div class="form_radio_btn zap">
	<input id="radio-2{{$key}}" type="radio" name="selected_price[{{$key}}]" value="2" @if ($sparepart->selected_price == 2)
         checked
     @endif>
	<label for="radio-2{{$key}}">&nbsp{{$sparepart->price_analogue}}</label>
</div>
      </td>
      <td scope="col" class="
      @if($sparepart->selected_mrk==3&&$sparepart->selected_price==3)zapmrk @endif
      @if($sparepart->selected_mrk==3)mrk @endif
      @if($sparepart->selected_price==3)zap @endif
      ">
      <div class="form_radio_btn zap">
	<input id="radio-3{{$key}}" type="radio" name="selected_price[{{$key}}]" value="3" @if ($sparepart->selected_price == 3)
         checked
     @endif>
	<label for="radio-3{{$key}}">&nbsp {{$sparepart->price_used}}</label>
</div>
     </td>
     <td scope="col" class="
      @if($sparepart->selected_mrk==4&&$sparepart->selected_price==4)zapmrk @endif
      @if($sparepart->selected_mrk==4)mrk @endif
      @if($sparepart->selected_price==4)zap @endif
      ">
      <div class="form_radio_btn zap">
	<input id="radio-4{{$key}}" type="radio" name="selected_mrk[{{$key}}]" value="4" @if ($sparepart->selected_mrk == 4)
         checked
     @endif>
	<label for="radio-4{{$key}}">&nbsp </label>
</div>
     </td>
     @endif

      @if ((Auth::user()->GroupId==5))
          
      
      <td scope="col" class="
      @if($sparepart->selected_mrk==1&&$sparepart->selected_price==1)zapmrk @endif
      @if($sparepart->selected_mrk==1)mrk @endif
      @if($sparepart->selected_mrk==1)zap @endif
      ">
      <div class="form_radio_btn mrk">
	<input id="radio-1{{$key}}" type="radio" name="selected_mrk[{{$key}}]" value="1" @if ($sparepart->selected_mrk == 1)
         checked
     @endif>
	<label for="radio-1{{$key}}">&nbsp{{$sparepart->price_original}}</label>
</div>
      
    </td>
      <td scope="col" class="
      @if($sparepart->selected_mrk==2&&$sparepart->selected_price==2)zapmrk @endif
      @if($sparepart->selected_mrk==2)mrk @endif
      @if($sparepart->selected_mrk==2)zap @endif
      ">
      <div class="form_radio_btn mrk">
	<input id="radio-2{{$key}}" type="radio" name="selected_mrk[{{$key}}]" value="2" @if ($sparepart->selected_mrk == 2)
         checked
     @endif>
	<label for="radio-2{{$key}}">&nbsp{{$sparepart->price_analogue}}</label>
</div>
      </td>
      <td scope="col" class="
      @if($sparepart->selected_mrk==3&&$sparepart->selected_price==3)zapmrk @endif
      @if($sparepart->selected_mrk==3)mrk @endif
      @if($sparepart->selected_mrk==3)zap @endif
      ">
      <div class="form_radio_btn mrk">
	<input id="radio-3{{$key}}" type="radio" name="selected_mrk[{{$key}}]" value="3" @if ($sparepart->selected_mrk == 3)
         checked
     @endif>
	<label for="radio-3{{$key}}">&nbsp {{$sparepart->price_used}}</label>
</div>
     </td>
     <td scope="col" class="
      @if($sparepart->selected_mrk==4&&$sparepart->selected_price==4)zapmrk @endif
      @if($sparepart->selected_mrk==4)mrk @endif
      @if($sparepart->selected_mrk==4)zap @endif
      ">
      <div class="form_radio_btn mrk">
	<input id="radio-4{{$key}}" type="radio" name="selected_mrk[{{$key}}]" value="4" @if ($sparepart->selected_mrk == 4)
         checked
     @endif>
	<label for="radio-4{{$key}}">&nbsp </label>
</div>
     </td>
     @endif
      <td scope="col">{{$sparepart->status}}</td>
      <td scope="col">{{$sparepart->date_price}}</td>
      <td scope="col">{{$sparepart->date_wait}}</td>
      <td scope="col"> <a onclick="if (!confirm('Точно удалить запчасть <{{$sparepart->name}}?>')) return false"  href="{{route('SparePart.destroy', $sparepart->id)}}" class="nav-link"><img src="/storage/img/delete.png" width="30" alt="delete"></a>
      </td>
    </tr>
      
@endforeach
 </tbody>
</table>

<input class="nav-link" type="submit" value="Сохранить">
</form>
<div class="container">
 @endsection 