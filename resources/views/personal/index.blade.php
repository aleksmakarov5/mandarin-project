@extends('layout')
@section('title')
    Личный кабинет
@endsection
@section('main_content')
    <form method="POST" action="/personal/update">

        <h1 class="h3 mb-3 fw-normal">Личный кабинет</h1>
        @csrf
        Выберите тему
        <div class="form_radio_btn dark">
            <input id="radio-5" type="radio" name="theme" value="dark" @if ($user->theme == 'dark') checked @endif>
            <label for="radio-5">темная</label>
        </div>
        <div class="form_radio_btn light">
            <input id="radio-6" type="radio" name="theme" value="light"
                @if ($user->theme == 'light') checked @endif>
            <label for="radio-6">светлая</label>
        </div>
        <div class="form-float">
            <input type="text" name='name' id="name" class="form-cont" id="floatingInput" placeholder="ФИО"
                value="{{ $user->name }}">
            <label for="floatingInput">ФИО</label>
            @error('name')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-float">
            <input type="email" name='email' id="email" class="form-cont" id="floatingInput"
                placeholder="name@example.com" value="{{ $user->email }}">
            <label for="floatingInput">Электронный адрес</label>
            @error('email')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-float">
            <input type="text" name='phone' id="phone" class="form-cont" id="floatingInput"
                value="{{ $user->phone }}">
            <label for="floatingInput">Номер телефона</label>
            @error('phone')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>


        <button class="nav-link" type="submit">Обновить</button>
    </form>
@endsection
