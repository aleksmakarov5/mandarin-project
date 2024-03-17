@extends('header_menu')
@section('title')
    Заявкa
@endsection
@section('main')
    @can('admin')
        <a href="/application/{{ $id }}/doc_load" class="nav-link">Загрузить данные из документа</a>
        <br>
        <a href="/applications/{{ $application->id }}/edit" class="nav-link">Изменить</a>
        <br>
        @if ($application->id_status == 2 && !$empty)
            <a href="/applications/{{ $application->id }}/send_mrk/{{ $application->id_MRK }}" class="nav-link">Передать МРК</a>
        @endif
        @if ($application->id_status == 5 || $application->id_status == 10)
            <a href="/answer_ic/{{ $id }}" class="nav-link">Подготовить ответ в ск</a>
        @endif
    @endcan
    @can('spm')
        @if ($application->id_status == 4 && !$empty)
            <a href="/applications/{{ $application->id }}/send_mrk/{{ $application->id_MRK }}" class="nav-link">Передать МРК</a>
        @endif
    @endcan
    @can('mrk')
        @if ($application->id_status == 3 && !$empty)
            <form action="/applications/{{ $application->id }}/change_status" method="POST">
                @csrf
                <select name="variant" id="var" class="form-control">
                    <option value="1">Передать запчастисту для предварительного расчета</option>
                    <option value="2">Принять в работу</option>
                    <option value="3">Отклонить по причине отказа клиента</option>
                    <option value="4">Отклонить по причине нецелесообразности ремонта</option>
                </select>
                <input type="submit" class="nav-link" value="Выбрать">
            </form>
        @endif
    @endcan
    @can('km')
        @if ($application->id_status == 6 || $application->id_status == 7)
            <form action="/applications/{{ $application->id }}/change_status_km" method="POST">
                @csrf
                <select name="variant" id="var" class="form-control">
                    <option value="1">Принять в работу</option>
                    <option value="2">Подготовить отказ</option>
                </select>
                <input type="submit" class="nav-link" value="Выбрать">
            </form>
        @endif
    @endcan
    <table id="demo" class="table table-striped-columns table-hover table-group-divider table-responsive">
        <tr>
            <th>№ заявки</th>
            <th>
                {{ $application->id }}
            </th>
        </tr>
        <tr>
            <th>Дата заявки</th>
            <th>
                {{ $application->created_at }}
            </th>
        </tr>
        <tr>
            <th>Дата последнего измененения</th>
            <th>
                {{ $application->updated_at }}
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
                {{ $application->DTPdate }}
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


        <tr>
            <th>Менеджер по работе с клиентами</th>
            <th>
                {{ $mrk }}
            </th>
        </tr>
        <tr>
            <th>Запчастист</th>
            <th>
                {{ $spm }}
            </th>
        </tr>
        <tr>
            <th>Текущий статус</th>
            <th>
                {{ $application->StatusName }}
            </th>
        </tr>
        @if (session('rout') == 'directions')
            <tr></tr>
            <tr>
                <th>Паспорт РФ</th>
                <th>
                    {{ $application->ClientPasport }}
                </th>
            </tr>
        @endif
        </tbody>
    </table>
@endsection
