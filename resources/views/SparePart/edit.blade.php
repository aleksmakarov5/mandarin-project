@extends('layout')
@section('title')
    Запчасти 
@endsection
@section('main_content') 


<a href="/SparePart/{{$id}}" class="nav-link">Отмена</a>
<form action="/SparePart/{{$id}}/update" method="POST">
@csrf

      <input type="hidden" name="id_application"  value='{{$id}}'>
      
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
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

    
    
    </tr>
      <tr>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text0" onkeyup="tableSearch(0,'demo')">
</th>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text1" onkeyup="tableSearch(1,'demo')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text2" onkeyup="tableSearch(2,'demo')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text3" onkeyup="tableSearch(3,'demo')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text4" onkeyup="tableSearch(4,'demo')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text5" onkeyup="tableSearch(5,'demo')"> </th>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text6" onkeyup="tableSearch(6,'demo')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text7" onkeyup="tableSearch(7,'demo')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder=" " id="search-text8" onkeyup="tableSearch(8,'demo')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text9" onkeyup="tableSearch(9,'demo')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text10" onkeyup="tableSearch(10,'demo')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text10" onkeyup="tableSearch(10,'demo')"></th>
     
      

    </tr>
  </thead>
  <tbody>

   
 
@foreach ($spareparts as $key=>$sparepart)
<tr  @if ( $sparepart->author==5)
  class="pink"
@endif>
    
      <td scope="col" id="td_id{{$key}}">{{$key}}</td> 
      <input type="hidden" name="id[{{$key}}]"  id='id{{$key}}' value='{{$sparepart->id}}'>
      <td id="td_name{{$key}}" scope="col">
          @if (old('name.'.$key)!==null)
        {{ old('name.'.$key) }}
     @else
         {{$sparepart->name}}
     @endif</td>
      <input type="hidden" name="name[{{$key}}]"  id='name{{$key}}' value='{{$sparepart->name}}'>
      <td id="td_part_number{{$key}}" scope="col">@if (old('part_number.'.$key)!==null)
        {{ old('part_number.'.$key) }}
     @else
         {{$sparepart->part_number}}
     @endif</td>
      <input type="hidden" name="part_number[{{$key}}]"  id='part_number{{$key}}' value='{{$sparepart->part_number}}'>
      <td id="td_order_number{{$key}}"  scope="col"> @if (old('order_number.'.$key)!==null)
        {{ old('order_number.'.$key) }}
     @else
         {{$sparepart->order_number}}
     @endif</td>
      <input type="hidden" name="order_number[{{$key}}]"  id='order_number{{$key}}' value='{{$sparepart->order_number}}'>
      <td id="td_price_ic_full{{$key}}" scope="col">@if (old('price_ic_full.'.$key)!==null)
        {{ old('price_ic_full.'.$key) }}
     @else
         {{$sparepart->price_ic_full}}
     @endif</td>
      <input type="hidden" name="price_ic_full[{{$key}}]"  id='price_ic_full{{$key}}' value='{{$sparepart->price_ic_full}}'>
      <td id="td_price_ic_half{{$key}}" scope="col" >@if (old('price_ic_half.'.$key)!==null)
        {{ old('price_ic_half.'.$key) }}
     @else
         {{$sparepart->price_ic_half}}
     @endif</td>
      <input type="hidden" name="price_ic_half[{{$key}}]"  id='price_ic_half{{$key}}' value='{{$sparepart->price_ic_half}}'>
      <td id="td_price_original{{$key}}" scope="col" class="
      @if($sparepart->selected_mrk==1&&$sparepart->selected_price==1)zapmrk @endif
      @if($sparepart->selected_mrk==1)mrk @endif
      @if($sparepart->selected_price==1)zap @endif
      ">@if (old('price_original.'.$key)!==null)
        {{ old('price_original.'.$key) }}
     @else
         {{$sparepart->price_original}}
     @endif</td>
      <input type="hidden" name="price_original[{{$key}}]"  id='price_original{{$key}}' value='{{$sparepart->price_original}}'>
      <td id="td_price_analogue{{$key}}" scope="col" class="
      @if($sparepart->selected_mrk==2&&$sparepart->selected_price==2)zapmrk @endif
      @if($sparepart->selected_mrk==2)mrk @endif
      @if($sparepart->selected_price==2)zap @endif
      ">
     @if (old('price_analogue.'.$key)!==null)
        {{ old('price_analogue.'.$key) }}
     @else
         {{$sparepart->price_analogue}}
     @endif
     
     </td>
      <input type="hidden" name="price_analogue[{{$key}}]"  id='price_analogue{{$key}}' value='{{$sparepart->price_analogue}}'>
      <td id="td_price_used{{$key}}" scope="col" class="
      @if($sparepart->selected_mrk==3&&$sparepart->selected_price==3)zapmrk @endif
      @if($sparepart->selected_mrk==3)mrk @endif
      @if($sparepart->selected_price==3)zap @endif
      ">@if (old('price_used.'.$key)!==null)
        {{ old('price_used.'.$key) }}
     @else
         {{$sparepart->price_used}}
     @endif</td>
      <input type="hidden" name="price_used[{{$key}}]"  id='price_used{{$key}}' value='{{$sparepart->price_used}}'>
      <td id="td_status{{$key}}" scope="col">@if (old('status.'.$key)!==null)
        {{ old('status.'.$key) }}
     @else
         {{$sparepart->status}}
     @endif</td>
      <input type="hidden" name="status[{{$key}}]"  id='status{{$key}}' value='{{$sparepart->status}}'>
      <td id="td_date_price{{$key}}" scope="col">
          <input type="date" name="date_price[{{$key}}]"  id='date_price{{$key}}' @if(old('date_price.'.$key)!=null)
        value='{{date(old('date_price.'.$key))}}'
     @else
         value='{{date($sparepart->date_price)}}'
     @endif> 
          
         </td>
       <td id="td_date_wait{{$key}}" scope="col">
           <input type="date" name="date_wait[{{$key}}]"  id='date_wait{{$key}}' @if(old('date_wait.'.$key)!=null)
        value='{{date(old('date_wait.'.$key))}}'
     @else
         value='{{date($sparepart->date_wait)}}'
     @endif> 
          
         </td>

     
</tr>
      
@endforeach
 </tbody>
</table>
<input class="nav-link" type="submit" value="Сохранить">
</form>
<script>
     function tableSearch(k,table) {
    var phrase = document.getElementById('search-text'+k);
    var table = document.getElementById(table);
    var regPhrase = new RegExp(phrase.value, 'i');
    var flag = false;
    for (var i = 2; i < table.rows.length; i++) {
        flag = false;
        flag = regPhrase.test(table.rows[i].cells[k].innerHTML);
        if (flag) {
            table.rows[i].style.display = "";
        } else {
            table.rows[i].style.display = "none";
        }

    }
}
// ИНИЦИАЛИЗАЦИЯ - ДВОЙНОЙ ЩЕЛЧОК ДЛЯ РЕДАКТИРОВАНИЯ ЯЧЕЙКИ
window.addEventListener("DOMContentLoaded", () => {
for (let cell of document.querySelectorAll("#demo td")) {
cell.ondblclick = () => { editable.edit(cell); };
}
});
let editable = {
// "ПРЕОБРАЗОВАТЬ" В РЕДАКТИРОВАННУЮ ЯЧЕЙКУ
edit: (cell) => {
// УДАЛИТЬ "ДВОЙНОЙ ЩЕЛЧОК ДЛЯ РЕДАКТИРОВАНИЯ"
cell.ondblclick = "";
// РЕДАКТИРУЕМОЕ СОДЕРЖИМОЕ
cell.contentEditable = true;
cell.focus();
// "ОТМЕТИТЬ" ТЕКУЩУЮ ВЫБРАННУЮ ЯЧЕЙКУ
        cell.classList.add("edit");
editable.selected = cell;
// НАЖМИТЕ ENTER ИЛИ ЩЕЛКНИТЕ ВНЕ, ЧТОБЫ ЗАВЕРШИТЬ РЕДАКТИРОВАНИЕ
window.addEventListener("click", editable.close);
cell.onkeydown = (evt) => { if (evt.key=="Enter") {
editable.close(true);
return false;
}};
},
// ЗАВЕРШИТЬ "РЕЖИМ РЕДАКТИРОВАНИЯ"
selected: null,  // текущая выбранная ячейка
close: (evt) => { if (evt.target != editable.selected) {
 
// УДАЛИТЬ "РЕДАКТИРОВАНИЕ"
window.getSelection().removeAllRanges();
editable.selected.contentEditable = false;
// ВОССТАНОВИТЬ КЛИК-СЛУШАТЕЛИ
    window.removeEventListener("click", editable.close);
    
let cell = editable.selected;
cell.ondblclick = () => { editable.edit(cell); };
// "СНЯТЬ ПОМЕТКУ" ТЕКУЩЕЙ ВЫБРАННОЙ ЯЧЕЙКИ
editable.selected.classList.remove("edit");
    editable.selected = null;
    // newvar = "Третий способ вывода переменной";
    let str = cell.id;
    let start = str.indexOf("_");
    let isid = document.getElementById(str.substring(start+1, str.length));
    isid.value = cell.innerHTML; 
}}
};


</script>
 @endsection 