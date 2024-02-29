@extends('layout')
@section('main_content')
   <a href="/troubleshooting/{{$troubleshooting->id_application}}" class="nav-link">Отмена</a>
<form action="/troubleshooting/{{$troubleshooting->id}}/update" method="POST">
     @csrf
    
<input name = "id_application" type="hidden" value="{{$troubleshooting->id_application}}">

<div class="form-floating">

 <input type="text" name="PartName" id="PartName"  class="form-control"  value="{{$troubleshooting->PartName}}">
      <label for="PartName">Наименование детали автомобиля</label>
</div>
  
<div class="form-floating">
 <input type="text" name="Work" id="Work"  class="form-control"  value="{{$troubleshooting->Work}}" >
      <label for="Work">Требуемое ремонтное воздействие</label>
</div>
<div class="form-floating">
 <input type="text" name="ExpertApinion" id="ExpertApinion"  class="form-control"  value="{{$troubleshooting->ExpertApinion}}" >
      <label for="ExpertApinion">Мнение эксперта</label>
</div>
<div class="form-floating">
 <input type="text" name="Сonclusion" id="Сonclusion"  class="form-control"  value="{{$troubleshooting->Conclusion}}" >
      <label for="Сonclusion">Заключение</label>
</div>

   <input type="submit" value="Обновить"    class="nav-link">

</form>
@endsection