@extends('layout')
@section('main_content')
    <a href="/applications" class="nav-link">Отмена</a>
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="/applications/store" method="POST">
        @csrf

        <div class="form-float">

            <select class="form-cont" id="id_insuranceCompany" name="id_insuranceCompany">
                @foreach ($InsuranceCompany as $item)
                    <option value="{{ $item->id }}" @if ($item->id === 4) selected @endif>{{ $item->name }}
                    </option>
                @endforeach

            </select>
            <label for="id_insuranceCompany">Страховая компания</label>
        </div>

        <div class="form-float">
            <input type="text" name="numberInsuranceCompanyApplication" id="numberInsuranceCompanyApplication"
                class="form-cont">
            <label for="numberInsuranceCompanyApplication">Номер заявки страховой компании</label>
        </div>

        <div class="form-float">
            <input type="text" name="CarModel" id="CarModel" class="form-cont">
            <label for="CarModel">Марка машины</label>
        </div>
        <div class="form-float">
            <input type="text" name="gnz" id="gnz" class="form-cont">
            <label for="gnz">ГНЗ</label>
        </div>
        <div class="form-float">
            <input type="text" name="vin" id="vin" class="form-cont">
            <label for="vin">ВИН</label>
        </div>
        <div class="form-float">
            <input type="text" name="ClientName" id="ClientName" class="form-cont">
            <label for="ClientName">ФИО клиента</label>
        </div>
        <div class="form-float">
            <input type="text" name="ClientName" id="ClientName" class="form-cont">
            <label for="ClientName">Телефон клиента</label>
        </div>
        <input type="submit" value="Создать" class="nav-link">

    </form>
@endsection
