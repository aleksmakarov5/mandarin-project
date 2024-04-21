@extends('admin.header_menu')
@section('main_content')
    <a href="/usergroups" class="nav-link">Отмена</a>
    <form method="POST" action="/usergroup_register">

        <h1 class="h3 mb-3 fw-normal">Новая группа пользователей</h1>
        @csrf
        <div class="form-floating">
            <input type="text" name='groupname' id="groupname" class="form-control" id="floatingInput" placeholder="ФИО">
            <label for="floatingInput">Имя группы</label>
            @error('groupname')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-floating">
            <input type="groupdescription" name='groupdescription' id="groupdescription" class="form-control"
                id="floatingInput" placeholder="name@example.com">
            <label for="floatingInput">Описание группы</label>
            @error('groupdescription')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <button class="nav-link" type="submit">Создать группу</button>
    </form>
@endsection
