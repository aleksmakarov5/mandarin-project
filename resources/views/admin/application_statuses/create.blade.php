@extends('admin.header_menu')
@section('main_content')
   <a href="/application_statuss" class="nav-link">Отмена</a>
 <form method="POST" action="/application_status_register">

    <h1 class="h3 mb-3 fw-normal">Новая группа</h1>
     @csrf
      <div class="form-floating">
      <input type="text" name='StatusName' id="StatusName" class="form-control" id="floatingInput" placeholder="Группа" value={{old('StatusName')}}>
      <label for="floatingInput">Статус заявки</label>
      @error('StatusName')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
   
   
    <button class="nav-link" type="submit">Создать Статус</button>
  </form>
@endsection