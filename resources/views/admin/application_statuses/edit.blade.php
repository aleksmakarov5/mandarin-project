@extends('admin.header_menu')
@section('main_content')
   <a href="/application_statuses" class="nav-link">Отмена</a>
 <form method="POST" action="/application_status/{{$application_status->id}}/update">

    <h1 class="h3 mb-3 fw-normal">Изменение данных статуса</h1>
     @csrf
      <div class="form-floating">
      <input type="text" name='StatusName' id="StatusName" class="form-control" id="floatingInput" placeholder="ФИО" value="{{$application_status->StatusName}}">
      <label for="floatingInput">Имя  группы</label>
      @error('StatusName')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
  
   
    <button class="nav-link" type="submit">Изменить</button>
  </form>
@endsection