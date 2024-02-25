@extends('layout')
@section('main_content')
   <a href="/SparePart/{{$sparepart->id_application}}" class="nav-link">Отмена</a>
   
       
  
<form action="/SparePart/{{$sparepart->id}}/update" method="POST">
     @csrf

<input name = "id_application" type="hidden" value="{{$sparepart->id_application}}">
@can('spm') 
<div class="form-floating">

 <input type="text" name="name" id="name"  class="form-control" value="{{$sparepart->name}}" >
      <label for="name">Наименование</label>
</div>
  
<div class="form-floating">
 <input type="text" name="part_number" id="part_number"  class="form-control"   value="{{$sparepart->part_number}}">
      <label for="part_number">Кат. номер</label>
</div>
<div class="form-floating">
 <input type="text" name="order_number" id="order_number"  class="form-control"   value="{{$sparepart->order_number}}">
      <label for="order_number">Заказанный номер</label>
</div>
<div class="form-floating">
 <input type="text" name="price_original" id="price_original"  class="form-control"   value="{{$sparepart->price_original}}">
      <label for="price_original">Цена оригинал</label>
      </div>
<div class="form-floating">
 <input type="text" name="price_analogue" id="price_analogue"  class="form-control"   value="{{$sparepart->price_analogue}}">
      <label for="price_analogue">Цена аналог</label>
  </div>
<div class="form-floating">
 <input type="text" name="price_used" id="price_used"  class="form-control"   value="{{$sparepart->price_used}}">
      <label for="price_used">Цена б/у</label>
    </div>
    <div >
    <label for="status"> Выбранный вариант <br></label>
<div class="form_radio_btn zap">
	<input id="radio-1" type="radio" name="selected_price" value="1" @if ($sparepart->selected_price == 1)
         checked
     @endif>
	<label for="radio-1">Оригинал</label>
</div>
 
<div class="form_radio_btn zap">
	<input id="radio-2" type="radio" name="selected_price" value="2"@if ($sparepart->selected_price == 2)
         checked
     @endif>
	<label for="radio-2">Аналог</label>
</div>
 
<div class="form_radio_btn zap">
	<input id="radio-3" type="radio" name="selected_price" value="3"@if ($sparepart->selected_price == 3)
         checked
     @endif>
	<label for="radio-3">Б/У</label>
</div>

<div class="form_radio_btn zap">
	<input id="radio-4" type="radio" name="selected_price" value="4"@if ($sparepart->selected_price == 4)
         checked
     @endif>
	<label for="radio-4">Ремонт</label>
</div>
</div>
<div>
  <label for="status"> <br>Пожелание клиента</label>
<div class="form_radio_btn mrk">
	<input id="radio-5" type="radio" name="selected_mrk" value="{{$sparepart->selected_mrk}}" checked>
<label for="radio-5">
     @switch($sparepart->selected_mrk)
         @case(1)
             Оригинал
             @break
         @case(2)
             Аналог
             @break
          @case(3)
            Б/У
             @break
         @case(4)
          Ремонт
          @break
         @default
             
     @endswitch
</label>
</div>
</div>
<div class="form-floating">
<input type="text" name="status" id="status"  class="form-control"   value="{{$sparepart->status}}">
      <label for="status"> Статус</label>
</div>
<div class="form-floating">
<input type="date" name="date_price" id="date_price"  class="form-control"  value="{{$sparepart->date_price}}" >
      <label for="date_price"> Дата заказа</label>
</div>
<div class="form-floating">
<input type="date" name="date_wait" id="date_wait"  class="form-control"  value="{{$sparepart->date_price}}" >
      <label for="date_wait">Ожидаемая дата получения</label>
</div>
<div >
       <label for="comment"> Комментарий</label>
<textarea name="comment" id="comment" cols=20 class="form-control" > 
{{$sparepart->comment}}
</textarea>
    
</div>
<div class="form-floating">
@endcan
@can('mrk')
    <table class="table table-striped-columns table-group-divider table-responsive">
 <tr>
     <td>
          Наименование
     </td>
      <td>
          {{$sparepart->name}}
      </td>
 </tr>
 <tr>
     <td>
          Кат. номер
     </td>
      <td>
          {{$sparepart->part_number}}
      </td>
 </tr>
 <tr>
     <td>
          Цена оригинал
     </td>
      <td>
          {{$sparepart->price_original}}
      </td>
 </tr>
 <tr>
     <td>
          Цена аналога
     </td>
     </td>
      <td>
          {{$sparepart->price_analogue}}
      </td>
 </tr>
 <tr>
     <td>
          Цена б/у
     </td>
      <td>
          {{$sparepart->price_used}}
      </td>
 </tr>
 <tr>
     <td>
          Выбранный вариант
     </td>
      <td>
          <div class="form_radio_btn zap">
	<input id="radio-6" type="radio" name="selected_price" value="{{$sparepart->selected_price}}" checked>
<label for="radio-6">
     @switch($sparepart->selected_price)
         @case(1)
             Оригинал
             @break
         @case(2)
             Аналог
             @break
          @case(3)
            Б/У
             @break
         @case(4)
          Ремонт
          @break
         @default
             
     @endswitch
</label>
</div>
      </td>
 </tr>
 <tr>
     <td>
          Пожелание клиента
     </td>
      <td>
     <div class="form_radio_btn mrk">
	<input id="radio-7" type="radio" name="selected_mrk" value="1" @if ($sparepart->selected_mrk == 1)
         checked
     @endif>
	<label for="radio-7">Оригинал</label>
     </div>
 
     <div class="form_radio_btn mrk">
	<input id="radio-8" type="radio" name="selected_mrk" value="2"@if ($sparepart->selected_mrk == 2)
         checked
     @endif>
	<label for="radio-8">Аналог</label>
     </div>
 
     <div class="form_radio_btn mrk">
	<input id="radio-9" type="radio" name="selected_mrk" value="3"@if ($sparepart->selected_mrk == 3)
         checked
     @endif>
	<label for="radio-9">Б/У</label>
</div>

<div class="form_radio_btn mrk">
	<input id="radio-10" type="radio" name="selected_mrk" value="4"@if ($sparepart->selected_mrk == 4)
         checked
     @endif>
	<label for="radio-10">Ремонт</label>
</div>
      </td>
 </tr>
    </table>
@endcan
   <input type="submit" value="Обновить"    class="nav-link">

</form>
@endsection