@extends('admin.header_menu')
@section('main_content')
   <a href="/application_statuses" class="nav-link">Отмена</a>
 <form method="POST" action="/application_status_register">

    <h1 class="h3 mb-3 fw-normal">Новая группа пользователей</h1>
     @csrf
      <div class="form-floating">
      <input type="text" name='StatusName' id="StatusName" class="form-control" id="floatingInput" placeholder="Группа" value={{old('StatusName')}}>
      <label for="floatingInput">Группа пользователя</label>
      @error('StatusName')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
    
    <button class="nav-link" type="submit">Создать статус</button>
  </form>
@endsection