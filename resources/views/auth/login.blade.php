@extends('layout')
@section('title')
    Аутентификация
@endsection
@section('main_content')  

<main class="form-signin w-100 m-auto">
  <form method="POST" action="\login_process">

    <h1 class="h3 mb-3 fw-normal">Пожалуйста авторизуйтесь</h1>
     @csrf
    <div class="form-floating">
      <input type="email" name='email' id="email" class="form-control" placeholder="name@example.com">
      <label for="email">Введите логин (ваш email)</label>
      @error('email')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
    <div class="form-floating">
      <input type="password" name="password" id="password"  class="form-control" placeholder="Password">
      <label for="password">Введите пароль</label>
       @error('password')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
    <button class="nav-link btn btn-primary w-100 py-2" type="submit">Войти</button>
  </form>
</main>
@endsection 