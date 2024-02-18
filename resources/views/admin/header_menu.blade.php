@extends('layout')
@section('title')
    Домашняя
@endsection
@section('main_content')

<header class="d-flex justify-content-center py-3">
      <ul class="nav nav-pills">
        <li class="nav-item"><a href="/admin" class="nav-link {{ request()->routeIs('admin') ? 'tn-active' : '' }}" aria-current="page">Основная информация</a></li>
         <li class="nav-item"><a href="/users" class="nav-link {{ request()->routeIs('users') ? 'tn-active' : '' }}" aria-current="page">Пользователи</a></li>
        <li class="nav-item"><a href="/statuses" class="nav-link {{ request()->routeIs('statuses') ? 'tn-active' : '' }}" aria-current="page">Статусы заявок</a></li>
       <li class="nav-item"><a href="/usergroups" class="nav-link {{ request()->routeIs('usergroups') ? 'tn-active' : '' }}" aria-current="page">Группы пользователей</a></li>
     <li class="nav-item"><a href="/inscompanies" class="nav-link {{ request()->routeIs('inscompanies') ? 'tn-active' : '' }}" aria-current="page">Страховые компании</a></li>
     
      </ul>
    </header>
@yield('main')
@endsection