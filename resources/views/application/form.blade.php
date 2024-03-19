@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('rout') == 'applications')
    <div class="form-float">

        <select class="form-cont" id="ur" name="ur">

            <option value=0>Клиент не работает с автоюристом</option>
            <option value=1 @if ($application->id_status == 25) selected @endif>Клиент работает с автоюристом</option>


        </select>
        <div class='lbl'><label for="id_insuranceCompany">Страховая компания</label></div>
    </div>
@endif

<div class="form-float">

    <select class="form-cont" id="id_insuranceCompany" name="id_insuranceCompany">
        @foreach ($InsuranceCompany as $item)
            <option value="{{ $item->id }}" @if ($item->id === $application->id_insuranceCompany) selected @endif>{{ $item->name }}
            </option>
        @endforeach

    </select>
    <div class='lbl'>
        <label for="id_insuranceCompany">Страховая компания</label>
    </div>
</div>

<div class="form-float">
    <input type="text" name="numberInsuranceCompanyApplication" id="numberInsuranceCompanyApplication"
        class="form-cont" value="{{ $application->numberInsuranceCompanyApplication }}">
    <div class='lbl'><label for="numberInsuranceCompanyApplication">Номер заявки страховой компании</label></div>
</div>

<div class="form-float">
    <input type="text" name="CarModel" id="CarModel" class="form-cont" value="{{ $application->CarModel }}">
    <div class='lbl'>
        <label for="CarModel">Марка машины</label>
    </div>
</div>
<div class="form-float">
    <input type="text" name="gnz" id="gnz" class="form-cont" value="{{ $application->gnz }}">
    <div class='lbl'><label for="gnz">ГНЗ</label></div>
</div>
<div class="form-float">
    <input type="text" name="vin" id="vin" class="form-cont" value="{{ $application->vin }}">
    <div class='lbl'>
        <label for="vin">ВИН</label>
    </div>
</div>
<div class="form-float">
    <input type="text" name="vehicle_passport" id="vehicle_passport" class="form-cont"
        value="{{ $application->vehicle_passport }}">
    <div class='lbl'><label for="vehicle_passport">ПТС/Свидетельство о регистрации</label></div>
</div>
<div class="form-float">
    <input type="text" name="Color" id="Color" class="form-cont" value="{{ $application->Color }}">
    <div class='lbl'>
        <label for="Color">Цвет</label>
    </div>
</div>
<div class="form-float">
    <input type="text" name="mileage" id="mileage" class="form-cont" value="{{ $application->mileage }}">
    <div class='lbl'><label for="mileage">Пробег</label></div>
</div>
<div class="form-float">
    <input type="date" name="DTPdate" id="DTPdate" class="form-cont" value="{{ $application->DTPdate }}">
    <div class='lbl'>
        <label for="DTPdate">Дата ДТП</label>
    </div>
</div>
<div class="form-float">
    <input type="text" name="ClientName" id="ClientName" class="form-cont" value="{{ $application->ClientName }}">
    <div class='lbl'><label for="ClientName">ФИО клиента</label></div>
</div>
<div class="form-float">
    <input type="text" name="ClientPhone" id="ClientPhone" class="form-cont"
        value="{{ $application->ClientPhone }}">
    <div class='lbl'>
        <label for="ClientPhone">Телефон клиента</label>
    </div>

</div>

<div class="form-float">

    <select class="form-cont" id="id_MRK" name="id_MRK">
        @foreach ($mrks as $item)
            <option value="{{ $item->id }}" @if ($item->id === $application->id_MRK) selected @endif>
                {{ $item->name }}
            </option>
        @endforeach

    </select>
    <div class='lbl'><label for="id_MRK">Менеджер по работе с клиентами</label></div>
</div>
<div class="form-float">

    <select class="form-cont" id="id_spm" name="id_spm">
        @foreach ($spms as $item)
            <option value="{{ $item->id }}" @if ($item->id === $application->id_spm) selected @endif>{{ $item->name }}
            </option>
        @endforeach

    </select>
    <div class='lbl'>
        <label for="id_spm">Запчастист</label>
    </div>
</div>
@if (session('rout') == 'directions')
    <hr>
    <div class="form-float">
        <input type="text" name="ub_number" id="ub_number" class="form-cont"
            value="{{ $application->ub_number }}">
        <div class='lbl'><label for="ub_number">№ убытка</label></div>
    </div>
    <div class="form-float">
        <input type="text" name="mnf_year" id="mnf_year" class="form-cont"
            value="{{ $application->mnf_year }}">
        <div class='lbl'>
            <label for="mnf_year">Год выпуска ТС</label>
        </div>
    </div>
    <div class="form-float">
        <input type="text" name="dogovor" id="dogovor" class="form-cont" value="{{ $application->dogovor }}">
        <div class='lbl'><label for="dogovor">Страховой полис</label></div>
    </div>

    <div class="form-float">

        <select class="form-cont" id="def" name="def">
            <option value="0" @if ($application->def == 0) selected @endif>Да</option>
            <option value="1"@if ($application->def == 1) selected @endif>Нет</option>
        </select>
        <div class='lbl'>
            <label for="def">Деффектовка во время ремонта</label>
        </div>
    </div>
    <div class="form-float">
        <input type="date" name="start_date" id="start_date" class="form-cont"
            value="{{ $application->start_date }}">
        <div class='lbl'><label for="start_date">Дата начала ремонта</label></div>
    </div>
    <div class="form-float">
        <input type="date" name="end_date" id="end_date" class="form-cont"
            value="{{ $application->end_date }}">
        <div class='lbl'>
            <label for="end_date">Дата окончания ремонта</label>
        </div>
    </div>
    <div class="form-float">
        <input type="text" name="wait_sp" id="wait_sp" class="form-cont" value="{{ $application->wait_sp }}">
        <div class='lbl'><label for="wait_sp">Срок поставки запчастей</label></div>
    </div>
    <h1>Паспорт РФ</h1>
    <div class="form-float">
        <input type="text" name="ClientPasport" id="ClientPasport" class="form-cont"
            value="{{ $application->ClientPasport }}">
        <div class='lbl'>
            <label for="ClientPasport">Серия</label>
        </div>
    </div>
    <div class="form-float">
        <input type="text" name="ClientPasport_number" id="ClientPasport_number" class="form-cont"
            value="{{ $application->ClientPasport }}">
        <div class='lbl'><label for="ClientPasport_number">Номер</label></div>
    </div>
    <div class="form-float">
        <input type="date" name="ClientPasport_date" id="ClientPasport_date" class="form-cont"
            value="{{ $application->ClientPasport_date }}">
        <div class='lbl'>
            <label for="ClientPasport_date">Дата выдачи</label>
        </div>
    </div>
    <div class="form-float">
        <input type="text" name="ClientPasport_struct" id="ClientPasport_struct" class="form-cont"
            value="{{ $application->ClientPasport_struct }}">
        <div class='lbl'><label for="ClientPasport_struct">Орган, выдавший паспорт</label></div>
    </div>
@endif
