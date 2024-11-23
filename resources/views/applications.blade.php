@extends('layout')
@section('title')
    Заявки
@endsection
@section('main_content')
    <h1>
        Все заявки</h1>

    <a href="/applications/create" class="nav-link">Добавить заявку</a>


    @if (Gate::allows('mrk') && !Gate::allows('admin'))
        @if (session('rout') == 'applications' || session('rout') == 'directions')
            <a href="?only_me={{ isset($only_me) && $only_me ? '0' : '1' }}"
                class="nav-link">{{ isset($only_me) && $only_me ? 'Показать только мои' : 'Показать все' }}</a>
        @endif
    @endif
    </div>
    <div id="app">
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                </ul>
            </div>
        @endif
        {{ $applications->onEachSide(5)->links('/vendor/pagination/default') }}
        <zayavki :user_id={{ Auth::user()->id }} :in_applications="{{ json_encode($applications->all()) }}"
            :rout=`{{ session('rout') }}` />

        {{-- <table id ="applications" class="editable">
            <thead>

                <tr>
                    <th scope="col">Дог</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Тайминг</th>
                    <th scope="col">#</th>

                    <th scope="col">МРК</th>
                    <th scope="col">Город</th>
                    <th scope="col">СК</th>
                    <th scope="col">СТО</th>
                    <th scope="col">Юрист</th>
                    <th scope="col">№ убытка</th>
                    <th scope="col">Клиент</th>
                    <th scope="col">Телефон</th>
                    <th scope="col">Авто</th>
                    <th scope="col">ГРЗ </th>
                    <th scope="col">Дата</th>
                    <th scope="col">Тип убытка</th>
                    <th scope="col">СТОА</th>
                    @if (session('rout') == 'directions')
                        @can('company')
                            <th scope="col">Сумма СК</th>
                            <th scope="col">Сумма З/Ч</th>
                            <th scope="col">Сумма ремонта</th>
                            <th scope="col">Затраты на ремонт</th>
                            <th scope="col">Оплата CК</th>
                            <th scope="col">Оплата СТО</th>
                            <th scope="col">Остаток</th>
                            <th scope="col"></th>
                        @endcan
                    @endif



                </tr>
                <tr>
                    <th></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="Статус" id="search-text1"
                            onkeyup="tableSearch(1,'applications')"></th>

                    <th scope="col"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text3"
                            onkeyup="tableSearch(3,'applications')">
                    </th>
                    <th scope="col"><input class="form-control" type="text" placeholder="МРК" id="search-text4"
                            onkeyup="tableSearch(4,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="Город" id="search-text5"
                            onkeyup="tableSearch(5,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="СК" id="search-text6"
                            onkeyup="tableSearch(6,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="СТО" id="search-text7"
                            onkeyup="tableSearch(7,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="Юрист" id="search-text8"
                            onkeyup="tableSearch(8,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="№ убытка " id="search-text9"
                            onkeyup="tableSearch(9,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="ФИО клиента"
                            id="search-text10" onkeyup="tableSearch(10,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="Телефон" id="search-text11"
                            onkeyup="tableSearch(11,'applications')"></th>


                    <th scope="col"><input class="form-control" type="text" placeholder="Авто " id="search-text12"
                            onkeyup="tableSearch(12,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="ГРЗ" id="search-text13"
                            onkeyup="tableSearch(13,'applications')"> </th>
                    <th scope="col"></th>


                    <th scope="col"><input class="form-control" type="text" placeholder="Тип убытка"
                            id="search-text14" onkeyup="tableSearch(14,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="СТОА" id="search-text15"
                            onkeyup="tableSearch(15,'applications')"></th>
                    @if (session('rout') == 'directions')
                        @can('company')
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        @endcan
                    @endif

                </tr>
            </thead>
            <tbody>



                @foreach ($applications as $application)
                    <tr @if (Auth::user()->GroupId != $application->current_group && Auth::user()->GroupId != 9) class="grey" @endif>
                        <td onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            @if ($application->doc)
                                <div class="td_round  yes"> да
                                </div>
                            @else
                                <div class="td_round  no"> нет
                                </div>
                            @endif
                        </td>
                        <td scope="col"
                            onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            <div class="td_round {{ $application->status_theme }}">
                                {{ $application->StatusName }}
                            </div>
                        </td>
                        @if ($application->count_hour > 0)
                            <td scope="col"
                                onclick="window.location.href='/application/{{ $application->id }}'; return false">

                                <div class="progressbar {{ $application->count_hour > 0 ? 'blue' : 'red' }}">
                                    <span
                                        style="width: {{ (($application->count_hour < $application->max_hour ? $application->count_hour : $application->max_hour) / $application->max_hour) * 100 }}%">{{ $application->count_hour > 0 ? $application->count_hour : 'просрочено' }}
                                        <span>
                                            @if ($application->id_status > 11 && $application->id_status != 18)
                                                {{ show_days($application->count_hour) }}
                                            @else
                                                {{ show_hours($application->count_hour) }}
                                            @endif
                                        </span>
                                    </span>

                                </div>
                        @if ($application->id_status < 5)
                                    <div class="progressbar {{ $application->count_five > 0 ? '' : 'red' }}">
                                        <span
                                            style="width: {{ ($application->count_five / 5) * 100 }}%">{{ $application->count_five > 0 ? $application->count_five : 'просрочено' }}
                                            <span>{{ show_hours($application->count_five) }}</span></span>
                                    </div>
                                @endif


                        </td>
                        @else
                            <td scope="col" class="alert alert-danger"
                                onclick="window.location.href='/application/{{ $application->id }}'; return false">
                                Просрочено
                            </td>
                        @endif
                        <td onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            <notify-counter :id_app={{ $application->id }} :user={{ Auth::user()->id }}
                                :count_in="{{ json_encode($application->count) }}" />

                        </td>
                        <td scope="col"
                            onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            @php
                                $mrk = $application->MRK_name;
                                $finish = mb_strpos($mrk, ' ');
                                if ($finish) {
                                    $mrk = mb_substr($mrk, 0, $finish);
                                }
                            @endphp
                            {{ $mrk }}
                        </td>
                        <td scope="col"
                            onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            {{ $application->city }}
                        </td>
                        <td scope="col"
                            onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            <div class="td_round  {{ $application->insurance_companies_theme }}">
                                {{ $application->name }} </div>
                        </td>
                        <td
                            scope="col"onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            <div class="td_round  {{ $application->services_theme }}"> {{ $application->service_name }}
                            </div>
                        </td>
                        <td
                            scope="col"onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            <div
                                class="td_round  @if ($application->lawyers_color) no
                            @else
                                yes @endif">
                                @php
                                    $lawyer_name = $application->lawyer_name;
                                    $finish = mb_strpos($lawyer_name, ' ');
                                    if ($finish) {
                                        $lawyer_name = mb_substr($lawyer_name, 0, $finish);
                                    }
                                @endphp
                                {{ $lawyer_name }} </div>
                        </td>

                        <td scope="col"
                            onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            {{ $application->numberInsuranceCompanyApplication }} </td>
                        <td onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            {{ $application->ClientName }}</td>
                        <td onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            {{ $application->ClientPhone }}</td>


                        <td scope="col"
                            onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            {{ $application->CarModel }} </td>
                        <td scope="col"
                            onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            {{ $application->gnz }}</td>
                        <td scope="col"
                            onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            {{ Date::parse($application->created_at)->format('j F Yг.') }}</td>

                        <td scope="col"
                            onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            {{ isset($application->ub_type) && $application->ub_type ? 'Каско' : 'Осаго' }}</td>
                        <td scope="col"
                            onclick="window.location.href='/application/{{ $application->id }}'; return false">
                            {{ $application->our_name }}</td>
                        @if (session('rout') == 'directions')
                            @can('company')
                                <td scope="col"
                                    onclick="window.location.href='/application/{{ $application->id }}'; return false">
                                    {{ $application->summ_ic_full }}</td>
                                <td scope="col"
                                    onclick="window.location.href='/application/{{ $application->id }}'; return false">
                                    {{ $application->summ_order }}</td>
                                <td scope="col"
                                    onclick="window.location.href='/application/{{ $application->id }}'; return false">
                                    {{ $application->summ_service }}</td>
                                <td scope="col"
                                    onclick="window.location.href='/application/{{ $application->id }}'; return false">
                                    {{ $application->summ_full }}</td>

                                <td scope="col"
                                    onclick="window.location.href='/application/{{ $application->id }}'; return false">
                                    {{ $application->ic_pay_date ? Date::parse($application->ic_pay_date)->format('j F Yг.') : '' }}
                                </td>
                                <td scope="col"
                                    onclick="window.location.href='/application/{{ $application->id }}'; return false">
                                    {{ $application->sto_pay_date ? Date::parse($application->sto_pay_date)->format('j F Yг.') : '' }}
                                </td>
                                <td scope="col"
                                    onclick="window.location.href='/application/{{ $application->id }}'; return false">
                                    <div
                                        class="
                @if ($application->proc > 61) alert alert-success
                         @else
                    alert alert-danger @endif
                          ">
                                        {{ $application->eco_delta_full }} ({{ $application->proc }}%)</div>
                                </td>
                            @endcan
                        @endif
                        <th scope="col">
                            <a onclick="if (!confirm('Точно удалить заявку №{{ $application->id }}?')) return false"
                                href="{{ route('applications.destroy', $application->id) }}" class="nav-link"><img
                                    src="/storage/img/delete.png" width="20" alt="delete"></a>
                        </th>
                    </tr>
                @endforeach
            </tbody>
        </table> --}}
    </div>

@endsection
