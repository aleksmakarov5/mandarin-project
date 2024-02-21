@extends('admin.header_menu')
@section('main_content')
   <a href="/insurance_companies" class="nav-link">Отмена</a>
 <form method="POST" action="/insurance_company_register">

    <h1 class="h3 mb-3 fw-normal">Новая СК</h1>
     @csrf
      <div class="form-floating">
      <input type="text" name='name' id="name" class="form-control" id="floatingInput" placeholder="Группа" value={{old('name')}}>
      <label for="floatingInput">Название</label>
      @error('name')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
      <div class="form-floating">
      <input type="text" name='address' id="address" class="form-control" id="floatingInput" placeholder="Группа" value={{old('address')}}>
      <label for="floatingInput">Адрес</label>
      @error('address')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
      <div class="form-floating">
      <input type="text" name='phone' id="phone" class="form-control" id="floatingInput" placeholder="Группа" value={{old('phone')}}>
      <label for="floatingInput">Телефон</label>
      @error('phone')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
     <div class="form-floating">
      <input type="email" name='email' id="email" class="form-control" id="floatingInput" placeholder="Группа" value={{old('email')}}>
      <label for="floatingInput">Email</label>
      @error('email')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
     <div class="form-floating">
      <input type="text" name='manager' id="manager" class="form-control" id="floatingInput" placeholder="Группа" value={{old('manager')}}>
      <label for="floatingInput">Менеджер</label>
      @error('manager')
          <p class="text-red-500">{{$message}}</p>
      @enderror
    </div>
   
   
    <button class="nav-link" type="submit">Создать СК</button>
  </form>
@endsection