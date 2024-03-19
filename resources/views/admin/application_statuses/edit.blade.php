@extends('admin.header_menu')
@section('main_content')
    <a href="/application_statuses" class="nav-link">Отмена</a>
    <form method="POST" action="/application_status/{{ $application_status->id }}/update">

        <h1 class="h3 mb-3 fw-normal">Изменение данных статуса</h1>
        @csrf
        <div class="form-floating">
            <input type="text" name='StatusName' id="StatusName" class="form-cont" id="floatingInput" placeholder="ФИО"
                value="{{ $application_status->StatusName }}">
            <label for="floatingInput">Имя группы</label>
            @error('StatusName')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-floating">

            <select class="form-cont" id="current_group" name="current_group">
                @foreach ($groups as $item)
                    <option value="{{ $item->id }}" @if ($item->id == $application_status->current_group) selected @endif>
                        {{ $item->GroupName }}</option>
                @endforeach

            </select>
            <label for="current_group">Текущая группа</label>
        </div>

        <button class="nav-link" type="submit">Изменить</button>
    </form>
@endsection
