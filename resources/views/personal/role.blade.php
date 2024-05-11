@extends('personal.header_menu')
@section('title')
    Личный кабинет
@endsection
@section('main')
    <form method="POST" action="/personal/role_update">

        <h1 class="h3 mb-3 fw-normal">Роль пользователя</h1>
        @csrf
        <div class='lbl'>
            <label for="id_spm">Роль</label>
        </div>
        <div class="form-float">

            <select class="form-cont" id="role" name="role">
                @foreach ($usergroups as $item)
                    <option value="{{ $item->id }}" @if ($item->id === session('GroupId')) selected @endif>
                        {{ $item->GroupName }}
                    </option>
                @endforeach

            </select>

        </div>


        <button class="nav-link" type="submit">Обновить</button>
    </form>
@endsection
