@extends('layout')
@section('main_content')
   <a href="/troubleshooting/{{$id}}" class="nav-link">Отмена</a>
<form action="/troubleshootings/store" method="POST">
     @csrf
    
<input name = "id_application" type="hidden" value="{{$id}}">

<div class="form-floating">

 <input type="text" name="PartName" id="PartName"  class="form-control"  >
      <label for="PartName">Наименование детали автомобиля</label>
</div>
  
<div class="form-floating">
 <input type="text" name="Work" id="Work"  class="form-control"  >
      <label for="Work">Требуемое ремонтное воздействие</label>
</div>
<div class="form-floating">
 <input type="text" name="ExpertApinion" id="ExpertApinion"  class="form-control"  >
      <label for="ExpertApinion">Мнение эксперта</label>
</div>
<div class="form-floating">
 <input type="text" name="Сonclusion" id="Сonclusion"  class="form-control"  >
      <label for="Сonclusion">Заключение</label>
</div>

   <input type="submit" value="Создать"    class="nav-link">

</form>
@endsection