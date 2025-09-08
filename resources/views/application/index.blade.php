@extends('header_menu')
@section('title')
    Заявкa
@endsection
@section('main')
    @canany(['admin', 'mrk', 'km', 'administrator'])
        <a href="/application/{{ $id }}/doc_load" class="nav-link">Загрузить данные из документа</a>
        <br>

        <a href="/applications/{{ $application->id }}/edit" class="nav-link">Изменить</a>
        <br>
    @endcanany

    <div class="row">
        <div class="col-4 col-sm-12 col-md-6 col-lg-4">
            <h3>Убыток</h3>
            <table id="demo" class="table table-striped-columns table-hover table-group-divider table-responsive">
                <tr>
                    <th>Страховая компания</th>
                    <th>
                        {{ $application->name }}
                    </th>
                </tr>
                <tr>
                    <th>Тип убытка</th>
                    <th>
                        @if ($application->ub_type == 0)
                            Осаго
                        @else
                            Каско
                        @endif
                    </th>
                </tr>
                <tr>
                    <th>№ заявки СК</th>
                    <th>
                        {{ $application->numberInsuranceCompanyApplication }}
                    </th>
                </tr>
                <tr>
                    <th>Дата ДТП</th>
                    <th>
                        {{ isset($application->DTPdate) ? Date::parse($application->DTPdate)->format('j F Yг.') : '' }}
                    </th>
                </tr>
                <tr>
                    <th>№ убытка</th>
                    <th>
                        {{ $application->ub_number }}
                    </th>
                </tr>
                <tr>
                    <th>Страховой полис</th>
                    <th>
                        {{ $application->dogovor }}
                    </th>
                </tr>
            </table>
        </div>
        <div class="col-4 col-sm-12 col-md-6 col-lg-4">
            <h3>Автомобиль</h3>
            <table id="demo" class="table table-striped-columns table-hover table-group-divider table-responsive">
                <tr>
                    <th>Марка машины</th>
                    <th>
                        {{ $application->CarModel }}
                    </th>
                </tr>
                <tr>
                    <th>ГНЗ</th>
                    <th>
                        {{ $application->gnz }}
                    </th>
                </tr>
                <tr>
                    <th>ВИН/Кузов</th>
                    <th>
                        {{ $application->vin }}
                    </th>
                </tr>
                <tr>
                    <th>ПТС/Свидетельство о регистрации</th>
                    <th>
                        {{ $application->vehicle_passport }}
                    </th>
                </tr>
                <tr>
                    <th>Цвет</th>
                    <th>
                        {{ $application->Color }}
                    </th>
                </tr>
                <tr>
                    <th>Пробег</th>
                    <th>
                        {{ $application->mileage }}
                    </th>
                </tr>
                <tr>
                    <th>Год выпуска ТС</th>
                    <th>
                        {{ $application->mnf_year }}
                    </th>
                </tr>
            </table>
        </div>
        <div class="col-4 col-sm-12 col-md-6 col-lg-4">
            <h3>Клиент</h3>
            <table id="demo" class="table table-striped-columns table-hover table-group-divider table-responsive">
                <tr>
                    <th>ФИО</th>
                    <th>
                        {{ $application->ClientName }}
                    </th>
                </tr>
                <tr>
                    <th>Пол</th>
                    <th>
                        @if ($application->gender == 0)
                            Мужской
                        @else
                            Женский
                        @endif
                    </th>
                </tr>
                <tr>
                    <th>Номер телефона клиента</th>
                    <th>
                        {{ $application->ClientPhone }}
                    </th>
                </tr>
                @if (isset($application->pred))
                    <tr>
                        <th>Представитель</th>
                        <th>
                            {{ $application->pred }}
                        </th>
                    </tr>
                    <tr>
                        <th>Номер телефона представителя</th>
                        <th>
                            {{ $application->pred_phone }}
                        </th>
                    </tr>
                @endif
                <tr>
                    <th>Дата рождения клиента</th>
                    <th>
                        {{ $application->birthday ? Date::parse($application->birthday)->format('j F Yг.') : '' }}
                    </th>
                </tr>
                <tr>
                    <th colspan=2>
                        <h1>Паспорт РФ</h1>
                    </th>

                </tr>

                <tr>
                    <th>Серия</th>
                    <th>
                        {{ $application->ClientPasport }}
                    </th>
                </tr>
                <tr>
                    <th>Номер</th>
                    <th>
                        {{ $application->ClientPasport_number }}
                    </th>
                </tr>
                <tr>
                    <th>Дата выдачи</th>
                    <th>
                        {{ $application->ClientPasport_date ? Date::parse($application->ClientPasport_date)->format('j F Yг.') : '' }}
                    </th>
                </tr>
                <tr>
                    <th>Орган, выдавший паспорт</th>
                    <th>
                        {{ $application->ClientPasport_struct }}
                    </th>
                </tr>
            </table>
        </div>
        <div class="col-6 col-sm-12 col-md-6 col-lg-6">
            <h3>Служебная информация</h3>
            <table id="demo" class="table table-striped-columns table-hover table-group-divider table-responsive">
                <tr>
                    <th>№ заявки</th>
                    <th>
                        {{ $application->id }}
                    </th>
                </tr>
                <tr>
                    <th>Автоюрист</th>
                    <th>
                        {{ $application->lawyer_name }}
                    </th>
                </tr>
                <tr>
                    <th>Дата заявки</th>
                    <th>
                        {{ Date::parse($application->created_at)->format('j F Yг. H:i') }}
                    </th>
                </tr>

                <tr>
                    <th>Менеджер по работе с клиентами</th>
                    <th>
                        {{ $application->mrk }}
                    </th>
                </tr>
                <tr>
                    <th>Куратор</th>
                    <th>
                        {{ $application->spm }}
                    </th>
                </tr>
                <tr>
                    <th>Сервис</th>
                    <th>
                        {{ $application->service_name }}
                    </th>
                </tr>

            </table>
        </div>
        <div class="col-6 col-sm-12 col-md-6 col-lg-6">
            <h3>-</h3>
            <table id="demo" class="table table-striped-columns table-hover table-group-divider table-responsive">

                <tr>
                    <th>Город</th>
                    <th>
                        {{ $application->city }}
                    </th>
                </tr>
                <tr>
                    <th>Текущий статус</th>
                    <th>
                        {{ $application->StatusName }}
                    </th>
                </tr>
                <tr>
                    <th>Деффектовка во время ремонта</th>
                    <th>
                        @if ($application->def == 0)
                            Да
                        @else
                            Нет
                        @endif
                    </th>
                </tr>
                <tr>
                    <th>Дата начала ремонта</th>
                    <th>
                        {{ $application->start_date ? Date::parse($application->start_date)->format('j F Yг.') : '' }}
                    </th>
                </tr>
                <tr>
                    <th>Дата окончания ремонта</th>
                    <th>
                        {{ $application->end_date ? Date::parse($application->end_date)->format('j F Yг.') : '' }}
                    </th>
                </tr>
                <tr>
                    <th>Срок поставки запчастей</th>
                    <th>
                        {{ $application->wait_sp }}
                    </th>
                </tr>
            </table>
        </div>

    </div>
@endsection
