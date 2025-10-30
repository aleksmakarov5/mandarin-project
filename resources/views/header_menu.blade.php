@php

    try {
        $app = App\Models\application::where('id', '=', $id)->first();
        if ($app->id_status < 12 && $app->id_status != 6 && $app->id_status != 7) {
            session(['rout' => 'applications']);
        }
        if ($app->id_status == 6 || $app->id_status == 7) {
            session(['rout' => 'failures']);
        }
        if ($app->id_status > 11 && $app->id_status < 25) {
            session(['rout' => 'directions']);
        }
        if ($app->id_status == 25) {
            session(['rout' => 'archive']);
        }
        if ($app->id_status == 27) {
            session(['rout' => 'temp']);
        }
        if ($app->id_status == 28) {
            session(['rout' => 'priem']);
        }
        $count = appNotification($app->id, Auth::user()->id);
        $max_hour = 1;
        $count_hour = 1;
        $status = App\Models\ApplicationStatus::get()->all();
        $f_status = App\Models\FinanceStatus::get()->all();
        $currentStatus = App\Models\ApplicationStatus::where('id', '=', $app->id_status)->first();
        $fin_status = App\Models\FinanceStatus::where('id', '=', $app->finance_statuses_id)->first();
        $ic = App\Models\InsuranceCompany::where('id', '=', $app->id_insuranceCompany)->first();
        $current = Carbon\Carbon::now();
        $spareparts = DB::table('spare_parts')->where('id_application', '=', $id)->get();
        $estimates = DB::table('estimates')->where('id_application', '=', $id)->first();
        $Works_serv = DB::table('works')->where('id_application', '=', $id)->get();
        $summ_service = 0;
        $summ_service_ic = 0;
        $summ_pzn = 0;
        foreach ($Works_serv as $work) {
            $summ_service = $summ_service + (float) str_replace(' ', '', $work->price_service);
            $summ_service_ic = $summ_service_ic + (float) str_replace(' ', '', $work->summ);
            $summ_pzn +=
                $work->count_hours_pzn == 0
                    ? str2number($work->summ)
                    : str2number($work->count_hours_pzn) * str2number($work->price);
        }
        $attachments = DB::table('attachments')->where('id_application', '=', $id)->get();
        $attach_statuses = [];
        $i = 0;
        if ($app->id_status < 11) {
            $end_date = Tochka\Calendar\WorkCalendar::create($app->created_at);
            $end_date->addWorkDays($currentStatus->time);
            // dd($end_date);
            $start = Carbon\Carbon::create($app->created_at);
            $end = Carbon\Carbon::create($end_date);
            // dd($end);
            $max_hour = $start->diffInHours($end);
            // dd($max_hour);
            // dd($current);
            //  dd($end);
            $count_hour = $max_hour - $current->diffInHours($start);
            // dd($count_hour);
        } elseif ($app->id_status == 11) {
            $end_date = Tochka\Calendar\WorkCalendar::create($app->check_date);
            $end_date->addWorkDays($currentStatus->time);

            $start = Tochka\Calendar\WorkCalendar::create($app->check_date);
            $max_hour = $start->diffInHours($end_date);
            $count_hour = $max_hour - $current->diffInHours($start);
        } elseif ($currentStatus->time != 0) {
            if (!$currentStatus->day) {
                $end_date = Tochka\Calendar\WorkCalendar::create($app->check_date);
                $end_date->addWorkDays($currentStatus->time);

                $start = Tochka\Calendar\WorkCalendar::create($app->check_date);
                $max_hour = $start->diffInHours($end_date);
                $count_hour = $max_hour - $current->diffInHours($start);
            } else {
                $end_date = Tochka\Calendar\WorkCalendar::create($app->check_date);
                $end_date->addWorkDays($currentStatus->time);

                $start = Tochka\Calendar\WorkCalendar::create($app->check_date);
                $max_hour = $start->diffInDays($end_date);
                $count_hour = $max_hour - $current->diffInDays($start);
            }
        } else {
            if ($app->id_status == 17) {
                $start = isset($app->start_date)
                    ? Carbon\Carbon::create($app->start_date)
                    : Carbon\Carbon::create($app->napr_date);

                //     $max_hour = $start->diffInDays($application->end_date)
                $max_hour = $start->diffInDays($app->end_date);
                $count_hour = $max_hour - $current->diffInDays($start);
            }
            if ($app->id_status == 13) {
                if ($count_hour = $current->diffInHours($app->view_date) < 24) {
                    $start = Carbon\Carbon::create($app->check_date);

                    //     $max_hour = $start->diffInDays($application->end_date)
                    $max_hour = $start->diffInHours($app->view_date);
                    $count_hour = $max_hour - $current->diffInHours($start);
                    $app->day = 0;
                } else {
                    $start = Carbon\Carbon::create($app->check_date);

                    //     $max_hour = $start->diffInDays($application->end_date)
                    $max_hour = $start->diffInDays($app->view_date);
                    $count_hour = $max_hour - $current->diffInDays($start);
                }
                // dd($application);
            }
            if ($app->id_status == 15) {
                $start = Carbon\Carbon::create($app->check_date);

                //     $max_hour = $start->diffInDays($application->end_date)
                $max_hour = $start->diffInDays($app->sp_date);
                $count_hour = $max_hour - $current->diffInDays($start);
                // dd($start, $application);
            }
        }
        if ($app->ic_answer_date != null) {
            $end_date = Tochka\Calendar\WorkCalendar::create($app->ic_answer_date);

            $start = Tochka\Calendar\WorkCalendar::create($app->ic_answer_date);
        } else {
            $end_date = Tochka\Calendar\WorkCalendar::now();

            $start = Tochka\Calendar\WorkCalendar::now();
        }

        $end_date->addWorkDays($fin_status->fin_time);
        if ($fin_status->fin_day) {
            $max_hour_f = $start->diffInDays($end_date);
            $count_hour_f = $max_hour_f - $current->diffInDays($start);
        } else {
            $max_hour_f = $start->diffInHours($end_date);
            $count_hour_f = $max_hour_f - $current->diffInHours($start);
        }

        foreach ($attachments as $attachment) {
            if ($attachment->id_status) {
                $attach_statuses[$i++] = $attachment->id_status;
            }
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
            $sparepart->price_ic_full = $app->ub_type ? $sparepart->price_ic_half : $sparepart->price_ic_full;

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
            switch ($sparepart->selected_mrk) {
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
                case 4:
                    $summ_order = $summ_order + str2number($sparepart->price_service);
                    break;
            }
        }
        if (isset($estimates->price_repair_full)) {
            $summ_ic_full = $estimates->price_repair_full;
            if (isset($estimates->add_pay)) {
                $summ_ic_full += str2number($estimates->add_pay);
            }
            // dd($esimates->price_repair_full);

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
                $eco_delta_full = $summ_ic_full - $summ_order - $summ_service - $app->sertificat;
            }
        } else {
            $eco_delta_full = $summ_ic_full + $summ_service_ic - $summ_order - $summ_service - $app->sertificat;
            if ($summ_ic_full + $summ_service_ic != 0) {
            }
        }
        if (isset($estimates->price_repair_half)) {
            $summ_ic_half = str2number($estimates->price_repair_half);
            $eco_delta_half = (float) str_replace(' ', '', $estimates->price_repair_half) - $summ_order - $summ_service;
        } else {
            $eco_delta_half = $summ_ic_half + $summ_service_ic - $summ_order - $summ_service;
        }
        if ($app->ub_type) {
            $summ_ic_full = $summ_ic_half;
            $eco_delta_full = $eco_delta_half;
        }
        if ($summ_ic_full != 0) {
            $proc = round(($eco_delta_full / $summ_ic_full) * 100);
        }
        $sum_teor +=
            $summ_pzn +
            (isset($estimates->price_zip)
                ? (float) str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $estimates->price_zip))
                : 0);
        $teor = $sum_teor - $summ_order - $summ_service - $app->sertificat;
        $proc_t = 0;
        if ($summ_ic_full + $summ_service_ic != 0 && $sum_teor != 0) {
            $proc_t = round(($teor / $sum_teor) * 100);
        }
    } catch (\Exception $e) {
        return redirect()->route('applications');
    }
    $users = DB::table('users')->get();

    // dd($work_groups);

    $km_s = DB::table('users')
        ->join('user_roles', function ($join) {
            $join->where('user_roles.id_group', '=', 8)->on('user_roles.id_user', '=', 'users.id');
        })
        ->join('role_companies', function ($join) use ($app) {
            $join
                ->where('role_companies.id_company', '=', $app->our_company)
                ->on('role_companies.id_role', '=', 'user_roles.id');
        })
        ->select('users.*')
        ->get();

    foreach ($km_s as $km) {
        $work_group = App\Models\work_group::firstorcreate(['id_user' => $km->id, 'id_application' => $id]);
    }

    if ($app->id_MRK) {
        $work_group = App\Models\work_group::firstorcreate(['id_user' => $app->id_MRK, 'id_application' => $id]);
    }

    $work_groups = App\Models\User::join('work_groups', 'work_groups.id_user', '=', 'users.id')
        ->where('work_groups.id_application', '=', $id)
        ->select('users.*')
        ->get();
    // dd($work_groups);
    $groups = DB::table('cities')->get();
    foreach ($groups as $group) {
        $group->members = DB::table('users')
            ->join('user_cities', function ($join) use ($group) {
                $join->where('user_cities.id_city', '=', $group->id)->on('user_cities.id_user', '=', 'users.id');
            })
            ->select('users.*')
            ->get();
    }

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

        $chat->attachment = App\Models\chat_attachment::where('chat_id', '=', $chat->id)->get();
    }
@endphp
@extends('layout')
@section('main_content')

    <div id="ch">
        <chat :app_id={{ $id }} :in_chats="{{ json_encode($chats) }}" :user_id={{ Auth::user()->id }}
            :in_users="{{ json_encode($users) }}" :count_in="{{ json_encode($count) }}"
            :in_groups="{{ json_encode($groups) }}" :in_work_groups="{{ json_encode($work_groups) }}"></chat>
    </div>

    <div id="app">
        <div class="table-content">


            @if (!isset($hide_menu))
                <table width=100%>
                    <tr>
                        <td>

                            <next-step :txt="`{{ $currentStatus->next_step }}`"></next-step>
                            {{--  --}}

                        </td>
                        <td align='right'>
                            <a href="/application/{{ $app->id }}/tg_load" class="msg_but" title="Открыть в Telegram"><img
                                    src="/storage/img/telegram.png" width="30" alt="Добавить"></a>
                            @can('company')
                                <a href="/combine/{{ $app->our_company }}/{{ $app->id }}" class="msg_but"
                                    title="Объединить заявки"
                                    onclick="if (!confirm('Объеденить заявку №{{ $app->id }} с другим делом?')) return false"><img
                                        src="/storage/img/edin.png" width="30" alt="Добавить"></a>
                                <a href="/applications/{{ $app->id }}/delete" class="msg_but" title="Удалить заявку"
                                    onclick="if (!confirm('Точно удалить заявку №{{ $app->id }}?')) return false"><img
                                        src="/storage/img/delete.png" width="30" alt="Удалить"></a>
                                <a href="/applications/{{ $app->id }}/move_temp" class="msg_but"
                                    title="Перенести в корзину"
                                    onclick="if (!confirm('Переместить в корзину заявку №{{ $app->id }}?')) return false"><img
                                        src="/storage/img/temp.png" width="30" alt="Удалить">
                                @endcan
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="app-lbl">
                                <table width="100%">
                                    <tr>
                                        <td>
                                            <h2>Заявка № {{ $id }} </h2>
                                        </td>
                                        <td>
                                            <a href="https://emex.ru/catalogs/original2/modifications?vin={{ $app->vin }}"
                                                target="_blank" class="nav-link">Emex</a>
                                        </td>
                                    </tr>
                                    @if (isset($app->pred_phone))
                                        <tr>
                                            <td colspan="2">
                                                <h5><b>Представитель:</b> {{ $app->pred }}</h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <h5>{{ $app->pred_phone }}</h5>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="2">
                                                <h5><b>Клиент:</b> {{ $app->ClientName }}</h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <h5>{{ $app->ClientPhone }}</h5>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="2">
                                            <h5>{{ $app->CarModel }} ({{ $app->mnf_year }} г.в.)</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <h5>{{ $app->gnz }}</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <h5>{{ $app->vin }}</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="td_round {{ $currentStatus->status_theme }}">
                                                {{ $currentStatus->StatusName }}
                                            </div>
                                        </td>



                                        <td>
                                            @if ($count_hour > 0)
                                                <div class="progressbar {{ $count_hour > 0 ? 'blue' : 'red' }}">
                                                    <span>
                                                        {{ $count_hour > 0 ? $count_hour : 'просрочено' }}
                                                        @if ($count_hour > 0)


                                                            @if ($currentStatus->day)
                                                                {{ show_days($count_hour) }}
                                                            @else
                                                                {{ show_hours($count_hour) }}
                                                            @endif
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="progressbar {{ $count_hour > 0 ? 'blue' : 'red' }}">
                                                    <span
                                                        style="width: {{ (($count_hour < $max_hour ? $count_hour : $max_hour) / $max_hour) * 100 }}%">

                                                    </span>


                                                </div>
                                            @else
                                                <div class="progressbar {{ $count_hour > 0 ? 'blue' : 'red' }}">
                                                    <span style="width: 100%;">просрочено
                                                        <span>
                                                </div>
                                            @endif
                                        </td>



                                        {{-- <div
                                                    class="
                                    'progressbar
                                    {{ $count_hour > 0 ? 'blue' : 'red' }}
                                ">
                                                    <span
                                                        style="width: '

                                                        {{ (($count_hour < $max_hour ? $count_hour : $max_hour) / $max_hour) * 100 }}
                                                            %';">{{ $count_hour > 0 ? $count_hour : 'просрочено' }}
                                                        @if ($count_hour > 0)
                                                            <span>

                                                                @if ($currentStatus->day)
                                                                    дн.
                                                                @else
                                                                    ч.
                                                                @endif
                                                            </span>
                                                        @endif

                                                    </span>
                                                </div> --}}

                                    </tr>
                                    <tr>
                                        <td>&nbsp </td>
                                        <td> &nbsp</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="td_round {{ $fin_status->FinStatusTheme }}">
                                                {{ $fin_status->FinStatusName }}
                                            </div>
                                        </td>
                                        @if ($fin_status->id > 1)



                                            <td>
                                                @if ($count_hour_f > 0)
                                                    <div class="progressbar {{ $count_hour_f > 0 ? 'blue' : 'red' }}">
                                                        <span>
                                                            {{ $count_hour_f > 0 ? $count_hour_f : 'просрочено' }}
                                                            @if ($count_hour_f > 0)
                                                                @if ($fin_status->fin_day)
                                                                    {{ show_days($count_hour_f) }}
                                                                @else
                                                                    {{ show_hours($count_hour_f) }}
                                                                @endif
                                                            @endif
                                                        </span>
                                                    </div>

                                                    <div class="progressbar {{ $count_hour_f > 0 ? 'blue' : 'red' }}">
                                                        <span
                                                            style="width: {{ (($count_hour_f < $max_hour_f ? $count_hour_f : $max_hour_f) / $max_hour_f) * 100 }}%">

                                                        </span>

                                                    </div>
                                                @else
                                                    <div class="progressbar {{ $count_hour_f > 0 ? 'blue' : 'red' }}">
                                                        <span style="width: 100%;">просрочено
                                                            <span>
                                                    </div>
                                                @endif


                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <h5>{{ $app->numberInsuranceCompanyApplication }}</h5>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div class="td_round {{ $ic->insurance_companies_theme }}">
                                                {{ $ic->name }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($app->ub_type)
                                                <div class="td_round blue" style='color:white;'>
                                                    Каско
                                                </div>
                                            @else
                                                <div class="td_round green">
                                                    Осаго
                                                </div>
                                            @endif
                                        </td>
                                    </tr>


                                </table>













                            </div>
                        </td>
                        @can('company')
                            <td>
                                @if (isset($eco_delta_full))
                                    <table class="editable">
                                        <thead>
                                            <tr align=center>
                                                <th colspan=2>Плановая</th>
                                                <th></th>
                                                <th colspan=2>Одобренная</th>
                                                <th></th>
                                                <th colspan=3>ДДС</th>
                                                <th colspan=5></th>
                                            </tr>
                                            <tr align=center>
                                                <th>
                                                    сумма:
                                                </th>
                                                <th>
                                                    выгода:
                                                </th>
                                                <th></th>
                                                <th>
                                                    сумма:
                                                </th>
                                                <th>
                                                    Выгода:
                                                </th>
                                                <th></th>
                                                <th>
                                                    Расходы:
                                                </th>
                                                <th>
                                                    Доходы:
                                                </th>
                                                <th>
                                                    Остаток:
                                                </th>
                                                <th></th>
                                                <th> Стоимость <br>запчастей</th>
                                                <th>Стоимость <br>работ</th>
                                                <th>ЗП</th>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <td>
                                                <div class="alert alert-success">
                                                    {{ in_money($sum_teor) }}
                                                    р.
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="
                                                                                    @if ($proc_t > 59) alert alert-success
                                                                                    @else
                                                                                    alert alert-danger @endif
                                                                                    ">
                                                    {{ in_money($teor) }} р. <br>({{ $proc_t }}%)
                                                </div>
                                            </td>
                                            <th></th>
                                            <td>
                                                <div class="alert alert-success">
                                                    {{ in_money($summ_ic_full) }}
                                                    руб;
                                                </div>
                                            </td>

                                            <td>
                                                <div
                                                    class="
                                                                                    @if ($proc > 59) alert alert-success
                                                                                    @else
                                                                                    alert alert-danger @endif
                                                                                    ">
                                                    {{ in_money($app->ub_type ? $eco_delta_half : $eco_delta_full) }} р.
                                                    <br> ({{ $proc }}%)
                                                </div>
                                            </td>
                                            <th></th>
                                            <td>
                                                <div class="alert alert-success">
                                                    0
                                                    р.
                                                </div>
                                            </td>
                                            <td>
                                                <div class="alert alert-success">
                                                    0
                                                    р.
                                                </div>
                                            </td>
                                            <td>
                                                <div class="alert alert-success">
                                                    0
                                                    р.
                                                </div>
                                            </td>
                                            <th></th>
                                            <td>
                                                <div class="alert alert-success">
                                                    {{ in_money($summ_order) }} р.
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="
                                                                                        @if ($summ_service > 0) alert alert-success
                                                                                        @else
                                                                                        alert alert-danger @endif
                                                                                        ">
                                                    {{ in_money($summ_service) }} р.
                                                </div>

                                            </td>
                                            <td>
                                                <div
                                                    class="
                                                                                    @if ($eco_delta_full > 0) alert alert-success
                                                                                    @else
                                                                                    alert alert-danger @endif
                                                                                    ">
                                                    @if ($app->our_company == 2)
                                                        {{ in_money(($eco_delta_full * 13) / 100) }} р.
                                                    @else
                                                        {{ in_money(($eco_delta_full * 20) / 100) }} р.
                                                    @endif
                                                </div>
                                            </td>
                                        @endcan
                                    </tr>
                                </table>
                            @endif
                            @canany(['buh', 'admin', 'administrator'])
                                @if (session('rout') == 'directions' || session('rout') == 'archive')
                                    <date-pay :app_id={{ $id }}
                                        :in_ic_pay_date="{{ json_encode($app->ic_pay_date) }}"
                                        :in_sto_pay_date="{{ json_encode($app->sto_pay_date) }}"
                                        :in_zp_date="{{ json_encode($app->zp_date) }}"
                                        :in_finish_pay_date="{{ json_encode($app->finish_pay_date) }}" />
                                @endif
                            @endcanany
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
                                <li><a href="" class="active">Статус</a>
                                    <ul class="submenu">
                                        {{-- @can('service')
                                            <li><a href="#">Передать
                                                    МРК</a></li>
                                        @endcan --}}
                                        {{-- @can('mrk')
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
                                        @endcan --}}
                                        {{-- @can('km')
                                            @if ($app->id_status == 6 || $app->id_status == 7)
                                                <li><a href="/applications/{{ $id }}/change_status_km/1">Принять
                                                        в
                                                        работу</a>
                                                </li>
                                                <li><a href="/applications/{{ $id }}/change_status_km/2">Подготовить
                                                        отказ</a></li>
                                            @endif
                                        @endcan --}}

                                        {{-- @can('spm')
                                            @if ($app->id_status == 4)
                                                <li><a href="/applications/{{ $id }}/send_mrk/{{ $app->id_MRK }}">Передать
                                                        МРК</a></li>
                                            @endif
                                        @endcan --}}
                                        {{-- @can('admin')
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
                                        @endcan --}}

                                        {{-- <li><a href="">Установить статус</a>
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



                                        </li> --}}
                                        @if ($app->id_status < 12)
                                            @if ($app->id_insuranceCompany == 4)
                                                <li><a onclick="if (!confirm('Изменить статус?')) return false"
                                                        href="/applications/{{ $id }}/change_st/11">
                                                        <div class="td_round  {{ $status[10]->status_theme }}">
                                                            {{ $status[10]->StatusName }}</div>
                                                    </a>
                                                </li>

                                            @endif
                                            <li><a onclick="if (!confirm('Изменить статус?')) return false"
                                                    href="/applications/{{ $id }}/change_st/12">
                                                    <div class="td_round  {{ $status[11]->status_theme }}">
                                                        {{ $status[11]->StatusName }}</div>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($app->id_status == 12 || $app->id_status == 14)
                                            <li><a @if (!$app->view_date) onclick="alert('Необходимо на вкладке <<Основная информация>> \n указать дату планируемого осмотра ')"
                                                    @else
                                                 onclick="if (!confirm('Изменить статус?')) return false"
                                                    href="/applications/{{ $id }}/change_st/13" @endif
                                                    @if ($app->id_status == 14) title="Дефектовка проведена, Т/С выдан клиенту до поступления запчастей"
                                                    @else
                                                        title="Клиент вызван на ремонт" @endif>
                                                    <div class="td_round  {{ $status[12]->status_theme }}">
                                                        {{ $status[12]->StatusName }}</div>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($app->id_status == 13)
                                            <li><a @if (!in_array(9, $attach_statuses)) onclick="alert('Отсутсвует актуальный скан акта приема автомобиля ')"
                                                    @else
                                                    @if (!in_array(10, $attach_statuses))
                                                        onclick="alert('Отсутсвуют актуальные фото приема автомобиля ')"
                                                    @else
                                                         onclick="if (!confirm('Изменить статус?')) return false"
                                                    href="/applications/{{ $id }}/change_st/14" @endif
                                                    @endif
                                                    title="Т/С поставлен на СТО">
                                                    <div class="td_round  {{ $status[13]->status_theme }}">
                                                        {{ $status[13]->StatusName }}</div>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($summ_order != 0)
                                            @if ($app->id_status == 14)
                                                <li><a @if ($app->id_lawyer > 2) @if (!in_array(30, $attach_statuses))
                                                        onclick="alert('Отсутсвуют фото дефектовки')"

                                                    @else
                                                    @if (!$app->sp_date) onclick="alert('Необходимо на вкладке <<Основная информация>> \n указать дату поставки З/Ч ')"

                                                    @else onclick="if (!confirm('Изменить статус?')) return false"
                                            href="/applications/{{ $id }}/change_st/15" @endif
                                                        @endif
                                                    @else
                                                        @if (!in_array(3, $attach_statuses)) onclick="alert('Отсутсвует скан договора с клиентом')"
                                                    @else
                                                    @if (!in_array(5, $attach_statuses)) onclick="alert('Отсутсвует скан соглашения с клиентом')"
                                                    @else
                                                    @if (!in_array(30, $attach_statuses))
                                                        onclick="alert('Отсутсвуют фото дефектовки')"
                                                    @else
                                                        @if (!$app->sp_date) onclick="alert('Необходимо на вкладке <<Основная информация>> \n указать дату поставки З/Ч ')"

                                                    @else
                                                         onclick="if (!confirm('Изменить статус?')) return false"
                                                    href="/applications/{{ $id }}/change_st/15" @endif
                                            @endif
                                        @endif
                                        @endif
                                        @endif>
                                        <div class="td_round  {{ $status[14]->status_theme }}">
                                            {{ $status[14]->StatusName }}</div>
                                        </a>
                                </li>
                                @endif
                                @if ($app->id_status == 15)
                                    <li><a onclick="if (!confirm('Изменить статус?')) return false"
                                            href="/applications/{{ $id }}/change_st/16">
                                            <div class="td_round  {{ $status[15]->status_theme }}">
                                                {{ $status[15]->StatusName }}</div>
                                        </a>
                                    </li>
                                @endif
                                @if ($app->id_status == 16)
                                    <li><a onclick="if (!confirm('Изменить статус?')) return false"
                                            href="/applications/{{ $id }}/change_st/17">
                                            <div class="td_round  {{ $status[16]->status_theme }}">
                                                {{ $status[16]->StatusName }}</div>
                                        </a>
                                    </li>
                                @endif
                            @else
                                @if ($app->id_status == 14)
                                    <li><a @if ($app->id_lawyer > 2) @if (!in_array(30, $attach_statuses))
                                                        onclick="alert('Отсутсвуют фото дефектовки')"
                                                    @else onclick="if (!confirm('Изменить статус?')) return false"
                                            href="/applications/{{ $id }}/change_st/17" @endif
                                        @else
                                            @if (!in_array(3, $attach_statuses)) onclick="alert('Отсутсвует скан договора с клиентом')"
                                                    @else
                                                    @if (!in_array(5, $attach_statuses)) onclick="alert('Отсутсвует скан соглашения с клиентом')"
                                                    @else
                                                    @if (!in_array(30, $attach_statuses))
                                                        onclick="alert('Отсутсвуют фото дефектовки')"
                                                    @else onclick="if (!confirm('Изменить статус?')) return false"
                                            href="/applications/{{ $id }}/change_st/17" @endif
                                            @endif
                                @endif
                                @endif>
                                <div class="td_round  {{ $status[16]->status_theme }}">
                                    {{ $status[16]->StatusName }}</div>
                                </a>
                    </li>
                    @endif
                    @endif
                    @if ($app->id_status == 17)
                        <li><a @if (!in_array(14, $attach_statuses)) onclick="alert('Отсутсвует скан акта сдачи автомобиля ')"
                                                    @else
                                                    @if (!in_array(15, $attach_statuses))
                                                        onclick="alert('Отсутсвуют фото сдачи автомобиля ')"
                                                    @else
                                                         onclick="if (!confirm('Изменить статус?')) return false"
                                                    href="/applications/{{ $id }}/change_st/18" @endif
                                @endif>
                                <div class="td_round  {{ $status[17]->status_theme }}">
                                    {{ $status[17]->StatusName }}</div>
                            </a>
                        </li>
                    @endif
                    @if ($app->id_status == 18)
                        <li><a
                                @if (!$app->ic_pay_date || !$app->sto_pay_date || !$app->zp_date) onclick="alert('Заполните таблицу с датами оплат!')"
                                                    @else

                                                         onclick="if (!confirm('Изменить статус?')) return false"
                                                    href="/applications/{{ $id }}/change_st/25" @endif>
                                <div class="td_round  {{ $status[24]->status_theme }}">
                                    {{ $status[24]->StatusName }}</div>
                            </a>
                        </li>
                    @endif
                    @if ($app->id_status == 27)
                        <li><a onclick="if (!confirm('Вернуть в работу?')) return false"
                                href="/applications/{{ $id }}/change_st/2">
                                <div class="td_round  {{ $status[1]->status_theme }}">
                                    Вернуть в работу</div>
                            </a>
                        </li>
                    @endif
                    @if ($app->id_insuranceCompany == 4 && !Gate::allows('administrator'))
                        <li>
                            <div class="td_round  {{ $status[19]->status_theme }}">
                                ----</div>

                        </li>
                        @for ($i = 1; $i < 12; $i++)
                            <li><a onclick="if (!confirm('Изменить статус?')) return false"
                                    href="/applications/{{ $id }}/change_st/{{ $i + 100 }}">
                                    <div class="td_round  {{ $f_status[$i - 1]->FinStatusTheme }}">
                                        {{ $f_status[$i - 1]->FinStatusName }}</div>
                                </a>
                            </li>
                        @endfor
                    @else
                        @if ($app->finance_statuses_id == 3)
                            <li><a onclick="if (!confirm('Изменить статус?')) return false"
                                    href="/applications/{{ $id }}/change_st/104">
                                    <div class="td_round  {{ $f_status[3]->FinStatusTheme }}">
                                        {{ $f_status[3]->FinStatusName }}</div>
                                </a>
                            </li>
                        @endif
                        @if ($app->finance_statuses_id == 5)
                            <li><a onclick="if (!confirm('Изменить статус?')) return false"
                                    href="/applications/{{ $id }}/change_st/106">
                                    <div class="td_round  {{ $f_status[5]->FinStatusTheme }}">
                                        {{ $f_status[5]->FinStatusName }}</div>
                                </a>
                            </li>
                        @endif
                        @if ($app->finance_statuses_id == 7)
                            <li><a onclick="if (!confirm('Изменить статус?')) return false"
                                    href="/applications/{{ $id }}/change_st/108">
                                    <div class="td_round  {{ $f_status[7]->FinStatusTheme }}">
                                        {{ $f_status[7]->FinStatusName }}</div>
                                </a>
                            </li>
                        @endif
                    @endif
                    @can('administrator')
                        <li>
                            <div class="td_round  {{ $status[19]->status_theme }}">
                                --Сис. админ--</div>

                        </li>
                        @for ($i = 1; $i < 28; $i++)
                            @if (
                                $i != 2 &&
                                    $i != 3 &&
                                    $i != 4 &&
                                    $i != 5 &&
                                    $i != 6 &&
                                    $i != 7 &&
                                    $i != 8 &&
                                    $i != 9 &&
                                    $i != 10 &&
                                    $i != 10 &&
                                    $i != 19 &&
                                    $i != 20 &&
                                    $i != 21 &&
                                    $i != 22 &&
                                    $i != 23 &&
                                    $i != 24 &&
                                    $i != 26)
                                <li><a onclick="if (!confirm('Изменить статус?')) return false"
                                        href="/applications/{{ $id }}/change_st/{{ $i }}">
                                        <div class="td_round  {{ $status[$i - 1]->status_theme }}">
                                            {{ $status[$i - 1]->StatusName }}</div>
                                    </a>
                                </li>
                            @endif
                        @endfor
                    @endcan

                </ul>

                </li>

                </ul>
                @can('administrator')
                    <ul class="dropbtn">
                        <li><a href="" class="active">Статус</a>
                            <ul class="submenu">

                                <li>
                                    <div class="td_round  {{ $status[19]->status_theme }}">
                                        ----</div>

                                </li>
                                @for ($i = 1; $i < 12; $i++)
                                    <li><a onclick="if (!confirm('Изменить статус?')) return false"
                                            href="/applications/{{ $id }}/change_st/{{ $i + 100 }}">
                                            <div class="td_round  {{ $f_status[$i - 1]->FinStatusTheme }}">
                                                {{ $f_status[$i - 1]->FinStatusName }}</div>
                                        </a>
                                    </li>
                                @endfor
                            </ul>
                        </li>
                    </ul>
                @endcan
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


                <li class="nav-item"><a href="/troubleshooting/{{ $id }}"
                        class="nav-link  {{ request()->routeIs('troubleshooting') ? 'tn-active' : '' }}"
                        aria-current="page">Дефектовка</a></li>
                @can('company')
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
    <div class="main-content">
        @yield('gallery')
    </div>
@endsection
