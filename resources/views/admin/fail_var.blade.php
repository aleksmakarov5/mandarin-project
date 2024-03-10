@extends('admin.header_menu')
@section('title')
  Страховые компании
@endsection
@section('main') 

<table id='demo' class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Название</th>
      <th></th>
    </tr>
  </thead>
  <tbody>

   
 
@foreach ($fail_vars as $key=>$fail_var)
<tr >
    
<form name="form{{$key}}" id='form{{$key}}' action="/fail_var/{{$fail_var->id}}" method="POST">
    @csrf
      <th scope="col">
        
        {{$fail_var->id}}
    
      <input type="hidden" name="id"  id='id-{{$key}}' value='{{$fail_var->id}}'>
      </th> 
      <td  id="td_variant-{{$key}}" scope="col">{{$fail_var->variant}} </td>
    
      <input type="hidden" name="variant"  id='variant-{{$key}}' value='{{$fail_var->variant}}'>
     <th scope="col">
         <a onclick="if (!confirm('Точно удалить вариант <{{$fail_var->variant}}?>')) return false" href="{{route('fail_var.destroy', $fail_var->id)}}" class="nav-link">Удалить</a>
        </th>
    </tr>
</form>
@endforeach
<tr>
  
<form action="/fail_var_register" method="POST">
     @csrf
  <th>  
</th><th> 
<div class="form-floating">

 <input type="text" name="variant" id="variant"  class="form-control"  >
     
</div>
 
     </th><th> 
   <input type="submit" value="Создать"    class="nav-link">
</th>
</form>
</tr>
 </tbody>
</table>
<script>
     function tableSearch(k,table) {
    var phrase = document.getElementById('search-text'+k);
    var table = document.getElementById(table);
    var regPhrase = new RegExp(phrase.value, 'i');
    var flag = false;
    for (var i = 3; i < table.rows.length; i++) {
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
    start=str.indexOf("-");
    let id=str.substring(start+1, str.length);
    isid.value = cell.innerHTML; 
    let form = document.getElementById('form'+id);
    form.submit();
}}
};
</script>
 @endsection 
