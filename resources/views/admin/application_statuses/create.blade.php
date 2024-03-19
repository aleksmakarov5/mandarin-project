@extends('admin.header_menu')
@section('main_content')
    <a href="/application_statuses" class="nav-link">Отмена</a>
    <form method="POST" action="/application_status_register">

        <h1 class="h3 mb-3 fw-normal">Новый статус</h1>
        @csrf
        <div class="form-floating">
            <input type="text" name='StatusName' id="StatusName" class="form-cont" id="floatingInput" placeholder="Группа"
                value={{ old('StatusName') }}>
            <label for="floatingInput">Статус заявки</label>
            @error('StatusName')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-floating">

            <select class="form-cont" id="current_group" name="current_group">
                @foreach ($groups as $item)
                    <option value="{{ $item->id }}">{{ $item->GroupName }}</option>
                @endforeach

            </select>
            <label for="current_group">Текущая группа</label>
        </div>


        <button class="nav-link" type="submit">Создать Статус</button>
    </form>
@endsection
