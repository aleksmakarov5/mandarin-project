@extends('layout')
@section('main_content')
   <a href="/Work/{{$id}}" class="nav-link">Отмена</a>
<form action="/Works/store" method="POST">
     @csrf
    
<input name = "id_application" type="hidden" value="{{$id}}">

<div class="form-floating">

 <input type="text" name="name" id="name"  class="form-control"  >
      <label for="name">Наименование</label>
</div>
  
<div class="form-floating">
 <input type="text" name="price" id="price"  class="form-control"  >
      <label for="price">Цена</label>
</div>
<div class="form-floating">
 <input type="text" name="count_hours" id="count_hours"  class="form-control"  >
      <label for="count_hours">Количество нормочасов</label>
</div>
<div class="form-floating">
 <input type="text" name="summ" id="summ"  class="form-control"  >
      <label for="summ">Сумма</label>
</div>
  <div class="form-floating">
 <input type="text" name="price_service" id="price_service"  class="form-control">
      <label for="summ">Цена сервиса</label>
     </div>
   <input type="submit" value="Создать"    class="nav-link">

</form>
@endsection