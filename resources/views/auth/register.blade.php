@extends('layout')
@section('title')
    Регистрация
@endsection
@section('main_content')  
<main class="form-signin w-100 m-auto">
  <form method="POST" action="\register_process">

    <h1 class="h3 mb-3 fw-normal">Регистрация</h1>
     @csrf
      <div class="form-floating">
      <input type="text" name='name' id="name" class="form-control" id="floatingInput" placeholder="ФИО">
      <label for="floatingInput">Ваше имя</label>
      @error('email')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
    <div class="form-floating">
      <input type="email" name='email' id="email" class="form-control" id="floatingInput" placeholder="name@example.com">
      <label for="floatingInput">Email address</label>
      @error('email')
          <p class="text-red-500">{{$message}}</p>
      @enderror
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
    <button class="tn-atom btn btn-primary w-100 py-2" type="submit">Регистрация</button>
  </form>
</main>
@endsection 