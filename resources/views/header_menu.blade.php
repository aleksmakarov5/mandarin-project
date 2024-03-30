@php
    use App\Models\application;
    use App\Models\ApplicationStatus;
    $app = application::where('id', '=', $id)->first();
    $status = ApplicationStatus::get()->all();
    $spareparts = DB::table('spare_parts')->where('id_application', '=', $id)->get();
    $estimates = DB::table('estimates')->where('id_application', '=', $id)->first();
    $Works_serv = DB::table('works')->where('id_application', '=', $id)->get();
    $summ_service = 0;
    $summ_service_ic = 0;
    foreach ($Works_serv as $work) {
        $summ_service = $summ_service + (float) str_replace(' ', '', $work->price_service);
        $summ_service_ic = $summ_service_ic + (float) str_replace(' ', '', $work->summ);
    }

    // dd($rewiew->all()) ;
    $summ_ic_full = 0;
    $summ_ic_half = 0;
    $summ_order = 0;
    $proc = 0;
    $eco_delta_full = 0;
    $eco_delta_half = 0;
    foreach ($spareparts as $i => $sparepart) {
        $summ_ic_full = $summ_ic_full + (float) str_replace(' ', '', $sparepart->price_ic_full);
        $summ_ic_half = $summ_ic_half + (float) str_replace(' ', '', $sparepart->price_ic_half);
        switch ($sparepart->selected_price) {
            case 1:
                $summ_order = $summ_order + (float) str_replace(' ', '', $sparepart->price_original);
                break;
            case 2:
                $summ_order = $summ_order + (float) str_replace(' ', '', $sparepart->price_analogue);
                break;
            case 3:
                $summ_order = $summ_order + (float) str_replace(' ', '', $sparepart->price_used);
                break;
        }
    }
    if (isset($estimates->price_repair_full)) {
        $eco_delta_full = (float) str_replace(' ', '', $estimates->price_repair_full) - $summ_order - $summ_service;
        $proc = round(($summ_order / ((float) str_replace(' ', '', $estimates->price_repair_full))) * 100);
    } else {
        $eco_delta_full = $summ_ic_full + $summ_service_ic - $summ_order - $summ_service;
        if ($summ_ic_full + $summ_service_ic != 0) {
            $proc = round(($summ_order / ($summ_ic_full + $summ_service_ic)) * 100);
        }
    }
    if (isset($estimates->price_repair_half)) {
        $eco_delta_half = (float) str_replace(' ', '', $estimates->price_repair_half) - $summ_order - $summ_service;
    } else {
        $eco_delta_half = $summ_ic_half + $summ_service_ic - $summ_order - $summ_service;
    }

@endphp
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
                @if (isset($eco_delta_full))
                    <table class="table-count">
                        <tr>
                            <td>
                                Остаток от полной стоимости:
                            </td>
                            <td> Стоимость запчастей</td>
                            <td>Стоимость работ</td>
                        </tr>
                        <tr>

                            <td>
                                <div
                                    class="
       @if ($eco_delta_full > 0) alert alert-success
       @else
       alert alert-danger @endif
       ">
                                    {{ $eco_delta_full }} руб;
                                </div>
                            </td>
                            <td>
                                <div
                                    class="
       @if ($proc < 21) alert alert-success
       @else
       alert alert-danger @endif
       ">
                                    {{ $summ_order }} руб; ({{ $proc }}%)
                                </div>
                            </td>
                            <td>
                                <div
                                    class="
       @if ($summ_service > 0) alert alert-success
       @else
       alert alert-danger @endif
       ">
                                    {{ $summ_service }} руб;
                                </div>
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
                    <button class="dropbtn">
                        @if ($app->id_status > 10 && $app->id_status < 23)
                            Установить статус
                        @else
                            Выберите действие
                        @endif

                    </button>
                    <div class="dropdown-content">
                        @can('mrk')
                            @if ($app->id_status == 9)
                                <a href="/answer_true_sel/{{ $id }}">Подготовить ответ</a>
                            @endif
                            @if ($app->id_status == 3)
                                <a href="/applications/{{ $id }}/change_status/1">Передать запчастисту для
                                    предварительного расчета</a>
                                <a href="/applications/{{ $id }}/change_status/2">Принять в работу</a>
                                <a href="/applications/{{ $id }}/change_status/3">Отклонить по причине отказа
                                    клиента</a>
                                <a href="/applications/{{ $id }}/change_status/4">Отклонить по причине
                                    нецелесообразности ремонта</a>
                            @endif

                            @if ($app->id_status > 11)
                                <a href="/answer_ic_mrk/{{ $id }}">Отправить в страховую акт скрытых</a>
                            @endif
                        @endcan
                        @can('km')
                            @if ($app->id_status == 6 || $app->id_status == 7)
                                <a href="/applications/{{ $id }}/change_status_km/1">Принять в работу</a>
                                <a href="/applications/{{ $id }}/change_status_km/2">Подготовить отказ</a>
                            @endif
                        @endcan
                        @canany(['mrk', 'admin', 'buh', 'km'])
                            @if ($app->id_status > 10 && $app->id_status < 23)
                                @foreach ($status as $stat)
                                    @if ($stat->id > $app->id_status && $stat->id < 23)
                                        <a
                                            href="/applications/{{ $id }}/change_st/{{ $stat->id }}">{{ $stat->StatusName }}</a>
                                    @endif
                                @endforeach
                            @endif
                        @endcanany
                        @can('spm')
                            @if ($app->id_status == 4)
                                <a href="/applications/{{ $id }}/send_mrk/{{ $app->id_MRK }}"
                                    class="nav-link">Передать МРК</a>
                            @endif
                        @endcan
                        @can('admin')
                            @if ($app->id_status == 2)
                                <a href="/applications/{{ $app->id }}/send_mrk/{{ $app->id_MRK }}"
                                    class="nav-link">Передать МРК</a>
                            @endif
                            @if ($app->id_status == 5)
                                <a href="/answer_ic/{{ $id }}" class="nav-link">Подготовить ответ в ск</a>
                                <br>
                            @endif
                            @if ($app->id_status == 10)
                                <a href="/answer_ic_true/{{ $id }}" class="nav-link">Подготовить ответ в ск</a>
                                <br>
                            @endif
                            @if ($app->id_status == 24 || $app->id_status == 25)
                                <a href="/applications/{{ $id }}/change_st/2">Вернуть в работу</a>
                                <br>
                            @endif
                        @endcan

                    </div>
                </div>

            </li>
            <li>
                <nav>
                    <ul class="topmenu">
                        <li><a href="" class="active">Главная<span class="fa fa-angle-down"></span></a>
                            <ul class="submenu">
                                <li><a href="">меню второго уровня</a></li>
                                <li><a href="">меню второго уровня<span class="fa fa-angle-down"></span></a>
                                    <ul class="submenu">
                                        <li><a href="">меню третьего уровня</a></li>
                                        <li><a href="">меню третьего уровня</a></li>
                                        <li><a href="">меню третьего уровня</a></li>
                                    </ul>
                                </li>
                                <li><a href="">меню второго уровня</a></li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </li>
        </ul>
    </header>
    @yield('main')
@endsection
