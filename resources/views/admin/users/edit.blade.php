@extends('admin.header_menu')
@section('main_content')
   <a href="/users" class="nav-link">Отмена</a>
 <form method="POST" action="/user/{{$user->id}}/update">

    <h1 class="h3 mb-3 fw-normal">Изменение данных пользователя</h1>
     @csrf
      <div class="form-floating">
      <input type="text" name='name' id="name" class="form-control" id="floatingInput" placeholder="ФИО" value="{{$user->name}}">
      <label for="floatingInput">ФИО</label>
      @error('name')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
    <div class="form-floating">
      <input type="email" name='email' id="email" class="form-control" id="floatingInput" placeholder="name@example.com"  value="{{$user->email}}">
      <label for="floatingInput">Электронный адрес</label>
      @error('email')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
     <div class="form-floating">
      <input type="text" name='phone' id="phone" class="form-control" id="floatingInput"  value="{{$user->phone}}">
      <label for="floatingInput">Номер телефона</label>
      @error('phone')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
    <div class="form-floating">
    
    <select class="form-control" id="floatingInput" name="groupid"  value="{{$user->groupid}}">
        @foreach ($groups as $item)
            <option value="{{$item->id}}"
@if ($item->id=== 2)
                    selected
                @endif
                >{{$item->GroupName}}</option>
        @endforeach
      
    </select>
    <label for="floatingInput">Группа</label>
  </div>
    <div class="form-floating">
      <input type="password" name="password" id="password"  class="form-control"  placeholder="Password">
      <label for="floatingPassword">Пароль</label>
      @error('password')
          <p class="text-red-500">{{$message}}</p>
      @enderror
      </div>
       <div class="form-floating">
      <input type="password" name="password_confirmation" id="password_confirmation"  class="form-control"  placeholder="Password">
      <label for="floatingPassword">Подтверждение пароля</label>
       
    </div>
    <button class="nav-link" type="submit">Изменить</button>
  </form>
@endsection