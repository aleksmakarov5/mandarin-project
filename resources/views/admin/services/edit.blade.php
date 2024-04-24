@extends('admin.header_menu')
@section('main_content')
    <a href="/services" class="nav-link">Отмена</a>
    <form method="POST" action="/services/{{ $service->id }}/update">

        <h1 class="h3 mb-3 fw-normal">Изменение данных сервиса</h1>
        @csrf
        <div class="form-floating">
            <input type="text" name='service_name' id="service_name" class="form-control" id="floatingInput"
                placeholder="Название" value="{{ $service->service_name }}">
            <label for="floatingInput">Название</label>
            @error('service_name')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-floating">
            <input type="text" name='service_description' id="service_description" class="form-control"
                id="floatingInput" value="{{ $service->service_description }}">
            <label for="service_description">Адрес</label>
            @error('service_description')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-floating">

            <select class="form-control" id="floatingInput" name="id_user" value="{{ $service->id_user }}">
                @foreach ($users as $item)
                    <option value="{{ $item->id }}" @if ($item->id === $service->id_user) selected @endif>
                        {{ $item->name }}</option>
                @endforeach

            </select>
            <label for="floatingInput">Пользователь</label>
        </div>


        <button class="nav-link" type="submit">Изменить</button>
    </form>
@endsection
