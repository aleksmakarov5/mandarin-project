@extends('personal.header_menu')
@section('title')
Личный кабинет
@endsection
@section('main')
<form method="POST" action="/personal/role_update">

    <h1 class="h3 mb-3 fw-normal">Телеграм</h1>
    @csrf
    <a href="/auth/telegram/callback" class="nav-link">Добавить телеграмм аккаунт</a>




    <button class="nav-link" type="submit">Обновить</button>
</form>
@endsection