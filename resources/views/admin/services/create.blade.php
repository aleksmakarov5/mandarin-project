@extends('admin.header_menu')
@section('main_content')
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <a href="/services" class="nav-link">Отмена</a>
    <form method="POST" action="/services/store">

        <h1 class="h3 mb-3 fw-normal">Создание нового сервиса</h1>
        @csrf
        <div class="form-floating">
            <input type="text" name='service_name' id="service_name" class="form-control" id="floatingInput"
                placeholder="Название">
            <label for="floatingInput">Название</label>
            @error('service_name')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-floating">
            <input type="text" name='service_description' id="service_description" class="form-control"
                id="floatingInput">
            <label for="service_description">Адрес</label>
            @error('service_description')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-floating">

            <select class="form-control" id="floatingInput" name="id_user">
                @foreach ($users as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->name }}</option>
                @endforeach

            </select>
            <label for="floatingInput">Пользователь</label>
        </div>

        <button class="nav-link" type="submit">Регистрация</button>
    </form>
@endsection
