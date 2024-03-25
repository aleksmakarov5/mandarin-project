@extends('layout')
@section('main_content')
    <a href="/application/{{ $id }}/doc_load" class="nav-link">Назад</a>
    <h1>Проверьте данные</h1>
    <form action="data_save" method="POST">
        @csrf

        <table class="table table-striped-columns table-hover table-group-divider table-responsive">
            @if (isset($price[1]))
                <tr>
                    <th>Cтоимость узлов и деталей:</th>
                    <th>
                        <input type="text" name="price_sp_full" value="{{ $price[0] }}">
                    </th>
                </tr>
                <tr>
                    <th>Cтоимость узлов и деталей c учетом износа:</th>
                    <th>
                        <input type="text" name="price_sp_half" value="{{ $price[1] }}">
                    </th>
                </tr>
            @endif
            @if (isset($price[3]))
                <tr>
                    <th>Cтоимость ремонта/замены:</th>
                    <th>
                        <input type="text" name="price_repair" value="{{ $price[2] }}">
                    </th>
                </tr>
                <tr>
                    <th>Cтоимость окраски контроля: </th>
                    <th>
                        <input type="text" name="price_paint" value="{{ $price[3] }}">
                    </th>
                </tr>
                <tr>
                    <th>Cтоимость расходных материалов: </th>
                    <th>
                        <input type="text" name="price_zip" value="{{ $price[4] }}">
                    </th>
                </tr>
                <tr>
                    <th>Cтоимость работ и материалов: </th>
                    <th>
                        <input type="text" name="price_work" value="{{ $price[5] }}">
                    </th>
                </tr>
                <tr>
                    <th>Cтоимость восстановительного ремонта (Всего) </th>
                    <th>
                        <input type="text" name="price_repair_full" value="{{ $price[6] }}">
                    </th>
                </tr>
                <tr>
                    <th>Затраты на восстановительный ремонт (с учетом износа): </th>
                    <th>
                        <input type="text" name="price_repair_half" value="{{ $price[7] }}">
                    </th>
                </tr>
            @endif
        </table>


        <table class="table table-striped-columns table-hover table-group-divider table-responsive">
            @if ($smeta)
                <tr>
                    <th>Марка машины</th>
                    <th>
                        <input type="text" name="CarModel" value="{{ $applications[1] }}">
                        {{-- {{ $application-> CarModel }} --}}

                    </th>
                </tr>
                <tr>
                    <th>ГНЗ</th>
                    <th>
                        <input type="text" name="gnz" value="{{ $applications[11] }}">


                        {{-- {{ $application-> gnz }} --}}
                    </th>
                </tr>
                <tr>
                    <th>ВИН/Кузов</th>
                    <th>
                        <input type="text" name="vin" value="{{ $applications[3] }}">


                        {{-- {{ $application-> vin }} --}}
                    </th>
                </tr>
                <tr>
                    <th>ПТС/Свидетельство о регистрации</th>
                    <th>

                        <input type="text" name="vehicle_passport" value="{{ $applications[7] }}">

                        {{-- {{ $application-> vehicle_passport }} --}}
                    </th>
                </tr>
                <tr>
                    <th>Цвет</th>
                    <th>

                        <input type="text" name="Color" value="{{ $applications[15] }}">
                        {{-- {{ $application-> Color }} --}}
                    </th>
                </tr>
                <tr>
                    <th>Пробег</th>
                    <th>

                        <input type="text" name="mileage" value="{{ $applications[5] }}">
                        {{-- {{ $application-> mileage }} --}}
                    </th>
                </tr>
                <tr>
                    <th>Дата ДТП</th>
                    <th>


                        <input type="text" name="DTPdate" value="{{ $applications[9] }}">
                        {{-- {{ $application-> DTPdate}} --}}
                    </th>
                </tr>
                <tr>
                    <th>ФИО клиента</th>
                    <th>

                        <input type="text" name="ClientName" value="{{ $applications[20] }}">
                        {{-- {{ $application-> ClientName }} --}}
                    </th>
                </tr>
            @else
                <tr>
                    <th>ФИО клиента</th>
                    <th>
                        <input type="text" name="ClientName" value="{{ $applications[2] }}">
                        {{-- {{ $application-> CarModel }} --}}

                    </th>
                </tr>
                <tr>
                    <th>ГНЗ</th>
                    <th>
                        <input type="text" name="gnz" value="{{ $applications[18] }}">


                        {{-- {{ $application-> gnz }} --}}
                    </th>
                </tr>
                <tr>
                    <th>ВИН/Кузов</th>
                    <th>
                        <input type="text" name="vin" value="{{ $applications[23] }}">


                        {{-- {{ $application-> vin }} --}}
                    </th>
                </tr>
                <tr>
                    <th>Марка/модель</th>
                    <th>

                        <input type="text" name="CarModel" value="{{ $applications[17] }}">

                        {{-- {{ $application-> vehicle_passport }} --}}
                    </th>
                </tr>
                <tr>
                    <th>Страховой полис</th>
                    <th>

                        <input type="text" name="dogovor" value="{{ $applications[19] }}">

                        {{-- {{ $application-> vehicle_passport }} --}}
                    </th>
                </tr>
                <tr>
                    <th>Год выпуска</th>
                    <th>

                        <input type="text" name="mnf_year" value="{{ $applications[24] }}">

                        {{-- {{ $application-> vehicle_passport }} --}}
                    </th>
                </tr>
                <tr>
                    <th>Пробег</th>
                    <th>

                        <input type="text" name="mileage" value="{{ intval($applications[25]) }}">

                        {{-- {{ $application-> vehicle_passport }} --}}
                    </th>
                </tr>
                <tr>
                    <th>Убыток №</th>
                    <th>

                        <input type="text" name="ub_number"
                            value="{{ mb_substr($applications[11], mb_strpos($applications[11], '/') + 1, mb_strlen($applications[11]) - mb_strpos($applications[11], '/')) }}">

                        {{-- {{ $application-> vehicle_passport }} --}}
                    </th>
                </tr>
            @endif




        </table>

        <button class="nav-link" type="submit">Сохранить</button>

    </form>
@endsection
