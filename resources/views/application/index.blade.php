@extends('header_menu')
@section('title')
    Заявкa
@endsection
@section('main')
    @canany(['admin', 'mrk'])
        <a href="/application/{{ $id }}/doc_load" class="nav-link">Загрузить данные из документа</a>
        <br>

        <a href="/applications/{{ $application->id }}/edit" class="nav-link">Изменить</a>
        <br>
    @endcanany



    <table id="demo" class="table table-striped-columns table-hover table-group-divider table-responsive">
        <tr>
            <th>Автоюрист</th>
            <th>
                {{ $application->lawyer_name }}
            </th>
        </tr>
        <tr>
            <th>№ заявки</th>
            <th>
                {{ $application->id }}
            </th>
        </tr>
        <tr>
            <th>Дата заявки</th>
            <th>
                {{ Date::parse($application->created_at)->format('j F Yг. H:i') }}
            </th>
        </tr>
        <tr>
            <th>Дата последнего измененения</th>
            <th>
                {{ Date::parse($application->updated_at)->format('j F Yг. H:i') }}
            </th>
        </tr>
        <tr>
            <th>Страховая компания</th>
            <th>
                {{ $application->name }}
            </th>
        </tr>
        <tr>
            <th>№ заявки страховой компании</th>
            <th>
                {{ $application->numberInsuranceCompanyApplication }}
            </th>
        </tr>
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
            <th>Дата ДТП</th>
            <th>
                {{ isset($application->DTPdate) ? Date::parse($application->DTPdate)->format('j F Yг.') : '' }}
            </th>
        </tr>
        <tr>
            <th>ФИО клиента</th>
            <th>
                {{ $application->ClientName }}
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
                <th>ФИО представителя</th>
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
            <th>Менеджер по работе с клиентами</th>
            <th>
                {{ $application->mrk }}
            </th>
        </tr>
        <tr>
            <th>Запчастист</th>
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
        <tr></tr>
        <tr>
            <th>№ убытка</th>
            <th>
                {{ $application->ub_number }}
            </th>
        </tr>
        <tr>
            <th>Год выпуска ТС</th>
            <th>
                {{ $application->mnf_year }}
            </th>
        </tr>
        <tr>
            <th>Страховой полис</th>
            <th>
                {{ $application->dogovor }}
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

        </tbody>
    </table>
@endsection
