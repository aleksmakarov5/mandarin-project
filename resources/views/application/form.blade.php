@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
            @endforeach
        </ul>
    </div>
@endif
<div class='lbl'><label for="id_MRK">Автоюрист</label><a href="/lawyers/create/{{ $application->id }}"><img
            src="/storage/img/add.png" width="20" alt="Добавить"></a></div>
<div class="form-float">


    <select class="form-cont" id="id_lawyer" name="id_lawyer">

        @foreach ($lawyers as $item)
            <option value="{{ $item->id }}" @if ($item->id === $application->id_lawyer) selected @endif>
                {{ $item->lawyer_name }}
            </option>
        @endforeach


    </select>

</div>

<label for="id_insuranceCompany">Страховая компания</label>
<div class="form-float">

    <select class="form-cont" id="id_insuranceCompany" name="id_insuranceCompany">
        @foreach ($InsuranceCompany as $item)
            <option value="{{ $item->id }}" @if ($item->id === $application->id_insuranceCompany) selected @endif>
                {{ $item->name }}
            </option>
        @endforeach

    </select>

</div>
<div class='lbl'>
    <label for="id_spm">Тип убытка</label>
</div>
<div class="form-float">

    <select class="form-cont" id="ub_type" name="ub_type">

            <option value="0" @if (0 === $application->ub_type) selected @endif>
                Осаго
            </option>
            <option value="1" @if (1 === $application->ub_type) selected @endif>
                Каско
            </option>


    </select>

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
<div class='lbl'>
    <label for="id_spm">Пол клиента</label>
</div>
<div class="form-float">

    <select class="form-cont" id="gender" name="gender">

        <option value="0" @if (0 === $application->gender) selected @endif>
            Мужской
        </option>
        <option value="1" @if (1 === $application->gender) selected @endif>
            Женскиий
        </option>


    </select>

</div>
<div class="form-float @error('ClientPhone')
    alert alert-danger
@enderror">

    <input type="text" name="ClientPhone" id="ClientPhone" class="form-cont" value="{{ $application->ClientPhone }}"
        placeholder="+7(000)000-00-00" data-phone-pattern>
    <div class='lbl'>
        <label for="ClientPhone">Телефон клиента</label>
    </div>

</div>
<div class="form-float">
    <input type="text" name="pred" id="pred" class="form-cont" value="{{ $application->pred }}">
    <div class='lbl'><label for="pred">Представитель</label></div>
</div>


<div class="form-float">
    <input type="text" name="pred_phone" id="pred_phone" class="form-cont"
        value="{{ $application->pred_phone }}" placeholder="+7(000)000-00-00" data-phone-pattern>
    <div class='lbl'>
        <label for="pred_phone">Телефон представителя</label>
    </div>
</div>

@canany(['admin', 'administrator'])

    <div class='lbl'><label for="id_MRK">Менеджер по работе с клиентами</label></div>
    <div class="form-float">

        <select class="form-cont" id="id_MRK" name="id_MRK">
            @foreach ($mrks as $item)
                <option value="{{ $item->user_id }}" @if ($item->user_id === $application->id_MRK) selected @endif>
                    {{ $item->name }}
                </option>
            @endforeach



        </select>

    </div>
    <div class='lbl'>
        <label for="id_spm">Запчастист</label>
    </div>
    <div class="form-float">

        <select class="form-cont" id="id_spm" name="id_spm">
            @foreach ($spms as $item)
                <option value="{{ $item->id }}" @if ($item->id === $application->id_spm) selected @endif>
                    {{ $item->name }}
                </option>
            @endforeach

        </select>

    </div>

@endcanany
<div class='lbl'>
    <label for="id_spm">Сервис</label>
</div>
<div class="form-float">

    <select class="form-cont" id="id_service" name="id_service">
        @foreach ($services as $item)
            <option value="{{ $item->id }}" @if ($item->id === $application->id_service) selected @endif>
                {{ $item->service_name }}
            </option>
        @endforeach

    </select>

</div>
<div class='lbl'>
    <label for="id_spm">Город</label>
</div>
<div class="form-float">

    <select class="form-cont" id="id_city" name="id_city">
        @foreach ($cities as $item)
            <option value="{{ $item->id }}" @if ($item->id === $application->id_city) selected @endif>
                {{ $item->name }}
            </option>
        @endforeach

    </select>

</div>



<hr>
<div class="form-float">
    <input type="text" name="ub_number" id="ub_number" class="form-cont" value="{{ $application->ub_number }}">
    <div class='lbl'><label for="ub_number">№ убытка</label></div>
</div>
<div class="form-float">
    <input type="text" name="mnf_year" id="mnf_year" class="form-cont" value="{{ $application->mnf_year }}">
    <div class='lbl'>
        <label for="mnf_year">Год выпуска ТС</label>
    </div>
</div>
<div class="form-float">
    <input type="text" name="dogovor" id="dogovor" class="form-cont" value="{{ $application->dogovor }}">
    <div class='lbl'><label for="dogovor">Страховой полис</label></div>
</div>

<div class="form-float">
    <input type="date" name="start_date" id="start_date" class="form-cont"
        value="{{ $application->start_date }}">
    <div class='lbl'><label for="start_date">Дата начала ремонта</label></div>
</div>

<div class="form-float">
    <input type="text" name="wait_sp" id="wait_sp" class="form-cont" value="{{ $application->wait_sp }}">
    <div class='lbl'><label for="wait_sp">Срок поставки запчастей</label></div>
</div>
<div class="form-float">
    <input type="date" name="birthday" id="birthday" class="form-cont" value="{{ $application->birthday }}">
    <div class='lbl'>
        <label for="birthday">Дата рождения клиента</label>
    </div>
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
        value="{{ $application->ClientPasport_number }}">
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
