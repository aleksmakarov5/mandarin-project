@extends('layout')
@section('title')
    Домашняя
@endsection
@section('main_content')
<h1>Заявка № {{$id}}  </h1> 
<header class="d-flex justify-content-center py-3">
      <ul class="nav nav-pills">
        <li class="nav-item"><a href="/application/{{$id}}" class="nav-link {{ request()->routeIs('application') ? 'tn-active' : '' }}" aria-current="page">Основная информация</a></li>
         <li class="nav-item"><a href="/SparePart/{{$id}}" class="nav-link  {{ request()->routeIs('SparePart') ? 'tn-active' : '' }}" aria-current="page">Запчасти</a></li>
        <li class="nav-item"><a href="/Work/{{$id}}" class="nav-link  {{ request()->routeIs('Work') ? 'tn-active' : '' }}" aria-current="page">Работы</a></li>
        @if (session('rout')=='directions')
             <li class="nav-item"><a href="#" class="nav-link  {{ request()->routeIs('priem') ? 'tn-active' : '' }}" aria-current="page">Приемка автомобиля</a></li>
        <li class="nav-item"><a href="/troubleshooting/{{$id}}" class="nav-link  {{ request()->routeIs('troubleshooting') ? 'tn-active' : '' }}" aria-current="page">Дефектовка</a></li>
        @endif
        <li class="nav-item"><a href="/mail/{{$id}}" class="nav-link {{ request()->routeIs('mail_app') ? 'tn-active' : '' }}" aria-current="page">Переписка с СК</a></li>
        <li class="nav-item"><a href="/chat/{{$id}}" class="nav-link {{ request()->routeIs('chat') ? 'tn-active' : '' }}" aria-current="page">Чат</a></li>
         <li class="nav-item"><a href="/document/{{$id}}" class="nav-link  {{ request()->routeIs('document') ? 'tn-active' : '' }}" aria-current="page">Документы</a></li>
       <li class="nav-item"><a href="/history/{{$id}}" class="nav-link {{ request()->routeIs('history') ? 'tn-active' : '' }}">История</a></li>
      </ul>
</header>
@yield('main')
@endsection