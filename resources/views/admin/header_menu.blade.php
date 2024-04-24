@extends('layout')
@section('title')
    Домашняя
@endsection
@section('main_content')
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="/admin" class="nav-link {{ request()->routeIs('admin') ? 'tn-active' : '' }}"
                    aria-current="page">Основная информация</a></li>
            <li class="nav-item"><a href="/users" class="nav-link {{ request()->routeIs('users') ? 'tn-active' : '' }}"
                    aria-current="page">Пользователи</a></li>
            <li class="nav-item"><a href="/usergroups"
                    class="nav-link {{ request()->routeIs('usergroups') ? 'tn-active' : '' }}" aria-current="page">Группы
                    пользователей</a></li>
            <li class="nav-item"><a href="/services"
                    class="nav-link {{ request()->routeIs('services') ? 'tn-active' : '' }}" aria-current="page">Сервисы</a>
            </li>
            <li class="nav-item"><a href="/application_statuses"
                    class="nav-link {{ request()->routeIs('application_statuses') ? 'tn-active' : '' }}"
                    aria-current="page">Статусы заявок</a></li>
            <li class="nav-item"><a href="/insurance_companies"
                    class="nav-link {{ request()->routeIs('insurance_companies') ? 'tn-active' : '' }}"
                    aria-current="page">Страховые компании</a></li>
            <li class="nav-item"><a href="/fail_var"
                    class="nav-link {{ request()->routeIs('fail_var') ? 'tn-active' : '' }}" aria-current="page">Варианты
                    отказа</a></li>

        </ul>
    </header>
    @yield('main')
@endsection
