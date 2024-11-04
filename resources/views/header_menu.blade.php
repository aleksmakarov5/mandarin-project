@php

    try {
        $app = App\Models\application::where('id', '=', $id)->first();
        $count = appNotification($app->id, Auth::user()->id);

        $status = App\Models\ApplicationStatus::get()->all();
        $currentStatus = App\Models\ApplicationStatus::where('id', '=', $app->id_status)->first();
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
        $sum_teor = 0;
        $proc = 0;
        $eco_delta_full = 0;
        $eco_delta_half = 0;
        foreach ($spareparts as $i => $sparepart) {
            $summ_ic_full =
                $summ_ic_full +
                (float) str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $sparepart->price_ic_full));
            $sum_teor +=
                $sparepart->price_sto == 0
                    ? (float) str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $sparepart->price_ic_full))
                    : (float) str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $sparepart->price_sto));
            $summ_ic_half =
                $summ_ic_half +
                (float) str_replace(
                    ' ',
                    '',
                    str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $sparepart->price_ic_half)),
                );
            switch ($sparepart->selected_price) {
                case 1:
                    $summ_order =
                        $summ_order +
                        (float) str_replace(
                            ' ',
                            '',
                            str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $sparepart->price_original)),
                        );
                    break;
                case 2:
                    $summ_order =
                        $summ_order +
                        (float) str_replace(
                            ' ',
                            '',
                            str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $sparepart->price_analogue)),
                        );
                    break;
                case 3:
                    $summ_order =
                        $summ_order +
                        (float) str_replace(
                            ' ',
                            '',
                            str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $sparepart->price_used)),
                        );
                    break;
            }
        }
        if (isset($estimates->price_repair_full)) {
            if ($estimates->emr10) {
                $eco_delta_full =
                    (float) str_replace(' ', '', $estimates->price_repair_full) * 1.1 -
                    $summ_order -
                    $summ_service -
                    $app->sertificat;
                if (((float) str_replace(' ', '', $estimates->price_repair_full)) != 0) {
                    $proc = round(
                        ($eco_delta_full / ((float) str_replace(' ', '', $estimates->price_repair_full) * 1.1)) * 100,
                    );
                }
            } else {
                $eco_delta_full =
                    (float) str_replace(' ', '', $estimates->price_repair_full) -
                    $summ_order -
                    $summ_service -
                    $app->sertificat;
                if (((float) str_replace(' ', '', $estimates->price_repair_full)) != 0) {
                    $proc = round(
                        ($eco_delta_full / ((float) str_replace(' ', '', $estimates->price_repair_full))) * 100,
                    );
                }
            }
        } else {
            $eco_delta_full = $summ_ic_full + $summ_service_ic - $summ_order - $summ_service - $app->sertificat;
            if ($summ_ic_full + $summ_service_ic != 0) {
                $proc = round(($eco_delta_full / ($summ_ic_full + $summ_service_ic)) * 100);
            }
        }
        if (isset($estimates->price_repair_half)) {
            $eco_delta_half = (float) str_replace(' ', '', $estimates->price_repair_half) - $summ_order - $summ_service;
        } else {
            $eco_delta_half = $summ_ic_half + $summ_service_ic - $summ_order - $summ_service;
        }
        $teor = $sum_teor + $summ_service_ic - $summ_order - $summ_service - $app->sertificat;
        $proc_t = 0;
        if ($summ_ic_full + $summ_service_ic != 0) {
            $proc_t = round(($teor / ($summ_ic_full + $summ_service_ic)) * 100);
        }
    } catch (\Exception $e) {
        return redirect()->route('applications');
    }
    $users = DB::table('users')->get();

    $chats = DB::table('chats')
        ->where('id_application', '=', $id)
        ->leftjoin('users as userfrom', 'chats.from', '=', 'userfrom.id')
        ->leftjoin('users as userto', 'chats.to', '=', 'userto.id')
        ->select('chats.*', 'userfrom.name as from_name', 'userto.name as to_name')
        ->get();

    foreach ($chats as $chat) {
        $chat->user_to = App\Models\chat_rec::where('id_chat', '=', $chat->id)
            ->join('users as userto', 'chat_recs.id_to', '=', 'userto.id')
            ->select('userto.name as to_name')
            ->get();
        $chat->time = Date::parse($chat->created_at)->format('j F Yг. H:i');
    }
@endphp
@extends('layout')
@section('title')
    Домашняя
@endsection
@section('main_content')
    <div id="ch">
        <chat :app_id={{ $id }} :in_chats="{{ json_encode($chats) }}" :user_id={{ Auth::user()->id }}
            :in_users="{{ json_encode($users) }}" :count_in="{{ json_encode($count) }}"></chat>
    </div>
    <div id="app">

        <div class="table-content">


            @if (!isset($hide_menu))
                <table class="table">
                    <tr>
                        <td>

                            <next-step :txt="`{{ $currentStatus->next_step }}`"></next-step>
                            {{--  --}}

                        </td>
                        <td align='right'>
                            <a href="/application/{{ $app->id }}/tg_load" class="msg_but" title="Открыть в Telegram"><img
                                    src="/storage/img/telegram.png" width="30" alt="Добавить"></a>
                            <a href="/combine/{{ $app->our_company }}/{{ $app->id }}" class="msg_but"
                                title="Объединить заявки"
                                onclick="if (!confirm('Объеденить заявку №{{ $app->id }} с другим делом?')) return false"><img
                                    src="/storage/img/edin.png" width="30" alt="Добавить"></a>
                            <a href="/applications/{{ $app->id }}/delete" class="msg_but" title="Удалить заявку"
                                onclick="if (!confirm('Точно удалить заявку №{{ $app->id }}?')) return false"><img
                                    src="/storage/img/delete.png" width="30" alt="Удалить"></a>
                            <a href="/applications/{{ $app->id }}/move_temp" class="msg_but"
                                title="Перенести во временные"
                                onclick="if (!confirm('Переместить во временные заявку №{{ $app->id }}?')) return false"><img
                                    src="/storage/img/temp.png" width="30" alt="Удалить">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="app-lbl">
                                <h1>Заявка № {{ $id }} </h1>
                                @if (isset($app->pred_phone))
                                    <h5><b>Представитель:</b></h5>
                                    <h5>{{ $app->pred }}</h5>
                                    <h5>{{ $app->pred_phone }}</h5>
                                @else
                                    <h5><b>Клиент:</b></h5>
                                    <h5>{{ $app->ClientName }}</h5>
                                    <h5>{{ $app->ClientPhone }}</h5>
                                @endif
                                <h5>{{ $app->CarModel }}</h5>
                                <h5>{{ $app->gnz }}</h5>


                                <h5>{{ $app->vin }}</h5>

                                <div class="td_round {{ $currentStatus->status_theme }}">{{ $currentStatus->StatusName }}
                                </div>
                            </div>
                        </td>
                        @can('company')
                            <td>
                                @if (isset($eco_delta_full))
                                    <table class="table-count">
                                        <tr>
                                            <td>
                                                Ожидаемая сумма:
                                            </td>
                                            <td>
                                                Теоретическая выгода:
                                            </td>
                                            <td>
                                                Остаток от полной стоимости:
                                            </td>
                                            <td> Стоимость запчастей</td>
                                            <td>Стоимость работ</td>
                                            <td>ЗП</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="alert alert-success">
                                                    {{ in_money(
                                                        $sum_teor +
                                                            (isset($estimates->price_repair)
                                                                ? (float) str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $estimates->price_repair))
                                                                : $summ_service_ic) +
                                                            (isset($estimates->price_paint)
                                                                ? (float) str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $estimates->price_paint))
                                                                : 0) +
                                                            (isset($estimates->price_zip)
                                                                ? (float) str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $estimates->price_zip))
                                                                : 0),
                                                    ) }}
                                                    руб;
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="
                                                            @if ($proc_t > 59) alert alert-success
                                                            @else
                                                            alert alert-danger @endif
                                                            ">
                                                    {{ in_money($teor) }} руб; ({{ $proc_t }}%)
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="
                                                            @if ($proc > 59) alert alert-success
                                                            @else
                                                            alert alert-danger @endif
                                                            ">
                                                    {{ in_money($eco_delta_full) }} руб; ({{ $proc }}%)
                                                </div>
                                            </td>
                                            <td>
                                                <div class="alert alert-success">
                                                    {{ in_money($summ_order) }} руб;
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="
                                                                @if ($summ_service > 0) alert alert-success
                                                                @else
                                                                alert alert-danger @endif
                                                                ">
                                                    {{ in_money($summ_service) }} руб;
                                                </div>

                                            </td>
                                            <td>
                                                <div
                                                    class="
                                                            @if ($eco_delta_full > 0) alert alert-success
                                                            @else
                                                            alert alert-danger @endif
                                                            ">
                                                    {{ in_money(($eco_delta_full * 2) / 10) }} руб;
                                                </div>
                                            </td>
                                        @endcan
                                    </tr>
                                </table>
                            @endif
                        </td>

                    </tr>
                </table>

        </div>
        <div class="menu-content">

            <header class="d-flex justify-content-center py-3">
                <ul class="nav nav-pills">
                    <li>
                        <nav>
                            <ul class="dropbtn">
                                <li><a href="" class="active">Выбрать действие</a>
                                    <ul class="submenu">
                                        @can('service')
                                            <li><a href="#">Передать
                                                    МРК</a></li>
                                        @endcan
                                        @can('mrk')
                                            @if (isset($app->id_service))
                                                <li><a href="#">Передать на СТО</a></li>
                                            @endif

                                            @if ($app->id_status == 9)
                                                <li><a href="/answer_true_sel/{{ $id }}">Подготовить
                                                        ответ</a>
                                                </li>
                                            @endif
                                            @if ($app->id_status == 3)
                                                <li><a href="/applications/{{ $id }}/change_status/1">Передать
                                                        запчастисту
                                                    </a></li>
                                                <li><a href="/applications/{{ $id }}/change_status/2">Принять
                                                        в
                                                        работу</a>
                                                </li>

                                                <li><a href="/applications/{{ $id }}/change_status/4">Отклонить
                                                    </a></li>
                                            @endif

                                            @if ($app->id_status > 11 && $app->id_status < 22)
                                                <li><a href="/answer_ic_mrk/{{ $id }}">Отправить в страховую
                                                        акт
                                                        скрытых
                                                        повреждений</a>
                                                </li>
                                            @endif
                                        @endcan
                                        @can('km')
                                            @if ($app->id_status == 6 || $app->id_status == 7)
                                                <li><a href="/applications/{{ $id }}/change_status_km/1">Принять
                                                        в
                                                        работу</a>
                                                </li>
                                                <li><a href="/applications/{{ $id }}/change_status_km/2">Подготовить
                                                        отказ</a></li>
                                            @endif
                                        @endcan

                                        @can('spm')
                                            @if ($app->id_status == 4)
                                                <li><a href="/applications/{{ $id }}/send_mrk/{{ $app->id_MRK }}">Передать
                                                        МРК</a></li>
                                            @endif
                                        @endcan
                                        @can('admin')
                                            @if ($app->id_status == 2 && $app->id_MRK)
                                                <li><a href="/applications/{{ $app->id }}/send_mrk/{{ $app->id_MRK }}">Передать
                                                        МРК</a></li>
                                            @endif
                                            @if ($app->id_status == 5 || $app->id_status == 1)
                                                <li><a href="/answer_ic/{{ $id }}">Подготовить ответ в
                                                        ск</a></li>
                                                <br>
                                            @endif
                                            @if ($app->id_status == 10)
                                                <li><a href="/answer_ic_true/{{ $id }}">Подготовить ответ в
                                                        ск</a></li>
                                                <br>
                                            @endif
                                            @if ($app->id_status == 21)
                                                <li><a href="/answer_ic_sch/{{ $id }}">Отправить счет и ПЗН в
                                                        СК</a>
                                                </li>
                                                <br>
                                            @endif
                                            @if ($app->id_status == 25 || $app->id_status == 26 || $app->id_status == 27)
                                                <li><a href="/applications/{{ $id }}/change_st/2">Вернуть в
                                                        работу</a>
                                                </li>
                                                <br>
                                            @endif
                                        @endcan

                                        <li><a href="">Установить статус</a>
                                            @can('admin')
                                                @if ($app->id_status == 3)
                                                    <ul class="submenu">
                                                        @foreach ($status as $stat)
                                                            @if ($stat->id == 2)
                                                                <li><a
                                                                        href="/applications/{{ $id }}/change_st/{{ $stat->id }}">{{ $stat->StatusName }}</a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            @endcan
                                            @canany(['mrk', 'admin', 'km'])
                                                @if ($app->id_status > 2 && $app->id_status < 26)
                                                    <ul class="submenu">
                                                        @foreach ($status as $stat)
                                                            @if ($stat->id > 7 && $stat->id < 26)
                                                                <li><a
                                                                        href="/applications/{{ $id }}/change_st/{{ $stat->id }}">
                                                                        <div class="td_round  {{ $stat->status_theme }}">
                                                                            {{ $stat->StatusName }}</div>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            @endcanany



                                        </li>
                                    </ul>
                                </li>

                            </ul>
                        </nav>
                    </li>
                    <li class="nav-item"><a href="/application/{{ $id }}"
                            class="nav-link {{ request()->routeIs('application') ? 'tn-active' : '' }}"
                            aria-current="page">Основная
                            информация</a>
                        <notify-app :id_app={{ $id }} :user={{ Auth::user()->id }} :type_in=1
                            :count_in="{{ json_encode($count) }}">
                    </li>
                    <li class="nav-item"><a href="/SparePart/{{ $id }}"
                            class="nav-link  {{ request()->routeIs('SparePart') ? 'tn-active' : '' }}"
                            aria-current="page">Смета</a></li>
                    {{-- <li class="nav-item"><a href="/Work/{{ $id }}"
                            class="nav-link  {{ request()->routeIs('Work') ? 'tn-active' : '' }}"
                            aria-current="page">Работы</a>
                    </li> --}}

                    @can('company')
                        <li class="nav-item"><a href="/troubleshooting/{{ $id }}"
                                class="nav-link  {{ request()->routeIs('troubleshooting') ? 'tn-active' : '' }}"
                                aria-current="page">Дефектовка</a></li>
                        <li class="nav-item"><a href="/mail/{{ $id }}"
                                class="nav-link {{ request()->routeIs('mail_app') ? 'tn-active' : '' }}"
                                aria-current="page">Переписка
                                с СК</a>
                            <notify-app :id_app={{ $id }} :user={{ Auth::user()->id }} :type_in=2
                                :count_in="{{ json_encode($count) }}">
                        </li>
                    @endcan
                    {{-- <li class="nav-item"><a href="/chat/{{ $id }}"
                            class="nav-link {{ request()->routeIs('chat') ? 'tn-active' : '' }}"
                            aria-current="page">Чат</a>

                    </li> --}}
                    <li class="nav-item"><a href="/document/{{ $id }}"
                            class="nav-link  {{ request()->routeIs('document') ? 'tn-active' : '' }}"
                            aria-current="page">Документы</a></li>

                    @can('company')
                        <li class="nav-item"><a href="/history/{{ $id }}"
                                class="nav-link {{ request()->routeIs('history') ? 'tn-active' : '' }}">История</a>
                        </li>
                    @endcan
                    <li></li>



                </ul>
            </header>


        </div>
        @endif
        <div class="main-content">
            @yield('main')
        </div>
    </div>

@endsection
