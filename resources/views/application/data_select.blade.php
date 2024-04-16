@extends('layout')
@section('main_content')
    <a href="/application/{{ $id }}/doc_load" class="nav-link">Назад</a>
    <h1>Проверьте данные</h1>
    <form action="data_save" method="POST">
        @csrf

        <input class="nav-link" type="submit" value="Сохранить">
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


            @if (isset($applications['CarModel']))
                <tr>
                    <th>Паспорт серия №</th>
                    <th>
                        <input type="text" name="CarModel" value="{{ $applications['CarModel'] }}">
                        {{-- {{ $application-> CarModel }} --}}

                    </th>
                </tr>
            @endif
            @if (isset($applications['CarModel']))
                <tr>
                    <th>Марка машины</th>
                    <th>
                        <input type="text" name="CarModel" value="{{ $applications['CarModel'] }}">
                        {{-- {{ $application-> CarModel }} --}}

                    </th>
                </tr>
            @endif
            @if (isset($applications['CarModel']))
                <tr>
                    <th>Марка машины</th>
                    <th>
                        <input type="text" name="CarModel" value="{{ $applications['CarModel'] }}">
                        {{-- {{ $application-> CarModel }} --}}

                    </th>
                </tr>
            @endif
            @if (isset($applications['CarModel']))
                <tr>
                    <th>Марка машины</th>
                    <th>
                        <input type="text" name="CarModel" value="{{ $applications['CarModel'] }}">
                        {{-- {{ $application-> CarModel }} --}}

                    </th>
                </tr>
            @endif
            @if (isset($applications['CarModel']))
                <tr>
                    <th>Марка машины</th>
                    <th>
                        <input type="text" name="CarModel" value="{{ $applications['CarModel'] }}">
                        {{-- {{ $application-> CarModel }} --}}

                    </th>
                </tr>
            @endif
            @if (isset($applications['CarModel']))
                <tr>
                    <th>Марка машины</th>
                    <th>
                        <input type="text" name="CarModel" value="{{ $applications['CarModel'] }}">
                        {{-- {{ $application-> CarModel }} --}}

                    </th>
                </tr>
            @endif
            @if (isset($applications['CarModel']))
                <tr>
                    <th>Марка машины</th>
                    <th>
                        <input type="text" name="CarModel" value="{{ $applications['CarModel'] }}">
                        {{-- {{ $application-> CarModel }} --}}

                    </th>
                </tr>
            @endif
            @if (isset($applications['gnz']))
                <tr>
                    <th>ГНЗ</th>
                    <th>
                        <input type="text" name="gnz" value="{{ $applications['gnz'] }}">


                        {{-- {{ $application-> gnz }} --}}
                    </th>
                </tr>
            @endif
            @if (isset($applications['vin']))
                <tr>
                    <th>ВИН/Кузов</th>
                    <th>
                        <input type="text" name="vin" value="{{ $applications['vin'] }}">


                        {{-- {{ $application-> vin }} --}}
                    </th>
                </tr>
            @endif
            @if (isset($applications['vehicle_passport']))
                <tr>
                    <th>ПТС/Свидетельство о регистрации</th>
                    <th>

                        <input type="text" name="vehicle_passport" value="{{ $applications['vehicle_passport'] }}">

                        {{-- {{ $application-> vehicle_passport }} --}}
                    </th>
                </tr>
            @endif
            @if (isset($applications['Color']))
                <tr>
                    <th>Цвет</th>
                    <th>

                        <input type="text" name="Color" value="{{ $applications['Color'] }}">
                        {{-- {{ $application-> Color }} --}}
                    </th>
                </tr>
            @endif
            @if (isset($applications['mileage']))
                <tr>
                    <th>Пробег</th>
                    <th>

                        <input type="text" name="mileage" value="{{ $applications['mileage'] }}">
                        {{-- {{ $application-> mileage }} --}}
                    </th>
                </tr>
            @endif
            @if (isset($applications['DTPdate']))
                <tr>
                    <th>Дата ДТП</th>
                    <th>


                        <input type="text" name="DTPdate" value="{{ $applications['DTPdate'] }}">
                        {{-- {{ $application-> DTPdate}} --}}
                    </th>
                </tr>
            @endif
            @if (isset($applications['ClientName']))
                <tr>
                    <th>ФИО клиента</th>
                    <th>

                        <input type="text" name="ClientName" value="{{ $applications['ClientName'] }}">
                        {{-- {{ $application-> ClientName }} --}}
                    </th>
                </tr>
            @endif
            @if (isset($applications['dogovor']))
                <tr>
                    <th>Страховой полис</th>
                    <th>

                        <input type="text" name="dogovor" value="{{ $applications['dogovor'] }}">

                        {{-- {{ $application-> vehicle_passport }} --}}
                    </th>
                </tr>
            @endif
            @if (isset($applications['mnf_year']))
                <tr>
                    <th>Год выпуска</th>
                    <th>

                        <input type="text" name="mnf_year" value="{{ $applications['mnf_year'] }}">

                        {{-- {{ $application-> vehicle_passport }} --}}
                    </th>
                </tr>
            @endif
            @if (isset($applications['ub_number']))
                <tr>
                    <th>Убыток №</th>
                    <th>

                        <input type="text" name="ub_number" value="{{ $applications['ub_number'] }}">

                        {{-- {{ $application-> vehicle_passport }} --}}
                    </th>
                </tr>
            @endif




        </table>


    </form>
@endsection
