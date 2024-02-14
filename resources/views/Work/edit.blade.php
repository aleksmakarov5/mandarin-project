@extends('layout')
@section('main_content')
   <a href="/Work/{{$Work->id_application}}" class="nav-link">Отмена</a>
<form action="/Work/{{$Work->id}}/update" method="POST">
     @csrf
    
<input name = "id_application" type="hidden" value="{{$Work->id_application}}">

<div class="form-floating">

 <input type="text" name="name" id="name"  class="form-control" value="{{$Work->name}}" >
      <label for="name">Наименование</label>
</div>
  
<div class="form-floating">
 <input type="text" name="price" id="price"  class="form-control"   value="{{$Work->price}}">
      <label for="price">Цена</label>
</div>
<div class="form-floating">
 <input type="text" name="count_hours" id="count_hours"  class="form-control"   value="{{$Work->count_hours}}">
      <label for="count_hours">Количество нормочасов</label>
</div>
<div class="form-floating">
 <input type="text" name="summ" id="summ"  class="form-control"   value="{{$Work->summ}}">
      <label for="summ">Цена оригинал</label>
     </div>

   <input type="submit" value="Обновить"    class="nav-link">

</form>
@endsection