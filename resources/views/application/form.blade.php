@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="form-floating">

    <select class="form-control" id="ur" name="ur">

        <option value=0>Клиент не работает с автоюристом</option>
        <option value=1 @if ($application->id_status == 25) selected @endif>Клиент работает с автоюристом</option>


    </select>
    <label for="id_insuranceCompany">Страховая компания</label>
</div>
<div class="form-floating">

    <select class="form-control" id="id_insuranceCompany" name="id_insuranceCompany">
        @foreach ($InsuranceCompany as $item)
            <option value="{{ $item->id }}" @if ($item->id === $application->id_insuranceCompany) selected @endif>{{ $item->name }}
            </option>
        @endforeach

    </select>
    <label for="id_insuranceCompany">Страховая компания</label>
</div>

<div class="form-floating">
    <input type="text" name="numberInsuranceCompanyApplication" id="numberInsuranceCompanyApplication"
        class="form-control" value="{{ $application->numberInsuranceCompanyApplication }}">
    <label for="numberInsuranceCompanyApplication">Номер заявки страховой компании</label>
</div>

<div class="form-floating">
    <input type="text" name="CarModel" id="CarModel" class="form-control" value="{{ $application->CarModel }}">
    <label for="CarModel">Марка машины</label>
</div>
<div class="form-floating">
    <input type="text" name="gnz" id="gnz" class="form-control" value="{{ $application->gnz }}">
    <label for="gnz">ГНЗ</label>
</div>
<div class="form-floating">
    <input type="text" name="vin" id="vin" class="form-control" value="{{ $application->vin }}">
    <label for="vin">ВИН</label>
</div>
<div class="form-floating">
    <input type="text" name="vehicle_passport" id="vehicle_passport" class="form-control"
        value="{{ $application->vehicle_passport }}">
    <label for="vehicle_passport">ПТС/Свидетельство о регистрации</label>
</div>
<div class="form-floating">
    <input type="text" name="Color" id="Color" class="form-control" value="{{ $application->Color }}">
    <label for="Color">Цвет</label>
</div>
<div class="form-floating">
    <input type="text" name="mileage" id="mileage" class="form-control" value="{{ $application->mileage }}">
    <label for="mileage">Пробег</label>
</div>
<div class="form-floating">
    <input type="date" name="DTPdate" id="DTPdate" class="form-control" value="{{ $application->DTPdate }}">
    <label for="DTPdate">Дата ДТП</label>
</div>
<div class="form-floating">
    <input type="text" name="ClientName" id="ClientName" class="form-control"
        value="{{ $application->ClientName }}">
    <label for="ClientName">ФИО клиента</label>
</div>
<div class="form-floating">
    <input type="text" name="ClientPhone" id="ClientPhone" class="form-control"
        value="{{ $application->ClientPhone }}">
    <label for="ClientPhone">Телефон клиента</label>

</div>

<div class="form-floating">

    <select class="form-control" id="id_MRK" name="id_MRK">
        @foreach ($mrks as $item)
            <option value="{{ $item->id }}" @if ($item->id === $application->id_MRK) selected @endif>{{ $item->name }}
            </option>
        @endforeach

    </select>
    <label for="id_MRK">Менеджер по работе с клиентами</label>
</div>
<div class="form-floating">

    <select class="form-control" id="id_spm" name="id_spm">
        @foreach ($spms as $item)
            <option value="{{ $item->id }}" @if ($item->id === $application->id_spm) selected @endif>{{ $item->name }}
            </option>
        @endforeach

    </select>
    <label for="id_spm">Запчастист</label>
</div>
@if (session('rout') == 'directions')
    <hr>
    <div class="form-floating">
        <input type="text" name="ClientPasport" id="ClientPasport" class="form-control"
            value="{{ $application->ClientPasport }}">
        <label for="ClientPasport">Паспорт РФ</label>
    </div>
@endif
