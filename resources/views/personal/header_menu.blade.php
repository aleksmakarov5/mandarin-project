@extends('layout')
@section('title')
    Личный кабинет
@endsection
@section('main_content')
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="/personal" class="nav-link {{ request()->routeIs('personal') ? 'tn-active' : '' }}"
                    aria-current="page">Основная информация</a></li>
            @can('administrator')
                <li class="nav-item"><a href="/personal/role"
                        class="nav-link {{ request()->routeIs('personal.role') ? 'tn-active' : '' }}" aria-current="page">Роль</a>
                </li>
            @endcan
            <li class="nav-item"><a href="/#" class="nav-link {{ request()->routeIs('users') ? 'tn-active' : '' }}"
                    aria-current="page">Пароль</a></li>
            <li class="nav-item"><a href="/#" class="nav-link {{ request()->routeIs('users') ? 'tn-active' : '' }}"
                    aria-current="page">email</a></li>
            <li class="nav-item"><a href="/#" class="nav-link {{ request()->routeIs('users') ? 'tn-active' : '' }}"
                    aria-current="page">telegram</a></li>



        </ul>
    </header>
    @yield('main')
@endsection
