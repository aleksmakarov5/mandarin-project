@extends('layout')
@section('title')
    Домашняя
@endsection
@section('main_content')
    <table class="table">
        <tr>
            <td>
                <h1>Заявка № {{ $id }} </h1>
            </td>
            <td>
                @if (isset($eco['delta_full']))
                    <table>
                        <tr>
                            <td>
                                Остаток от полной стоимости:
                            </td>
                            <td>
                                <div
                                    class="
       @if ($eco['delta_full'] > 0) alert alert-success
       @else
       alert alert-danger @endif
       ">
                                    {{ $eco['delta_full'] }} руб;
                                </div>
                            </td>
                            <td>
                                Остаток от стоимости c учетом износа:
                            </td>
                            <td>
                                <div
                                    class="
       @if ($eco['delta_half'] > 0) alert alert-success
       @else
       alert alert-danger @endif
       ">
                                    {{ $eco['delta_half'] }} руб; </div>
                            </td>
                        </tr>
                    </table>
                @endif
            </td>

        </tr>
    </table>

    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="/application/{{ $id }}"
                    class="nav-link {{ request()->routeIs('application') ? 'tn-active' : '' }}" aria-current="page">Основная
                    информация</a></li>
            <li class="nav-item"><a href="/SparePart/{{ $id }}"
                    class="nav-link  {{ request()->routeIs('SparePart') ? 'tn-active' : '' }}"
                    aria-current="page">Запчасти</a></li>
            <li class="nav-item"><a href="/Work/{{ $id }}"
                    class="nav-link  {{ request()->routeIs('Work') ? 'tn-active' : '' }}" aria-current="page">Работы</a>
            </li>
            @if (session('rout') == 'directions')
                <li class="nav-item"><a href="/troubleshooting/{{ $id }}"
                        class="nav-link  {{ request()->routeIs('troubleshooting') ? 'tn-active' : '' }}"
                        aria-current="page">Дефектовка</a></li>
            @endif
            <li class="nav-item"><a href="/mail/{{ $id }}"
                    class="nav-link {{ request()->routeIs('mail_app') ? 'tn-active' : '' }}" aria-current="page">Переписка
                    с СК</a></li>
            <li class="nav-item"><a href="/chat/{{ $id }}"
                    class="nav-link {{ request()->routeIs('chat') ? 'tn-active' : '' }}" aria-current="page">Чат</a></li>
            <li class="nav-item"><a href="/document/{{ $id }}"
                    class="nav-link  {{ request()->routeIs('document') ? 'tn-active' : '' }}"
                    aria-current="page">Документы</a></li>
            <li class="nav-item"><a href="/history/{{ $id }}"
                    class="nav-link {{ request()->routeIs('history') ? 'tn-active' : '' }}">История</a></li>
            <li></li>

            <li class="nav-item">
                <div class="dropdown">
                    <button class="dropbtn">Выберите действие</button>
                    <div class="dropdown-content">
                        @if ($application->id_status == 9)
                            <a href="/answer_true_sel/{{ $application->id }}">Подготовить ответ</a>
                            <a href="/answer_true_sel/{{ $application->id }}">Подготовить ответ</a>
                            <a href="/answer_true_sel/{{ $application->id }}">Подготовить ответ</a>
                        @endif

                    </div>
                </div>

            </li>
        </ul>
    </header>
    @yield('main')
@endsection
