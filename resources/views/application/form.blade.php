
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
    
    <select class="form-control" id="id_insuranceCompany" name="id_insuranceCompany">
        @foreach ($InsuranceCompany as $item)
            <option value="{{$item->id}}"
                @if ($item->id=== $application->id_insuranceCompany)
                    selected
                @endif
                >{{$item->name}}</option>
        @endforeach
      
    </select>
    <label for="id_insuranceCompany">Страховая компания</label>
  </div>

<div class="form-floating">
 <input type="text" name="numberInsuranceCompanyApplication" id="numberInsuranceCompanyApplication"  class="form-control"  value="{{$application->numberInsuranceCompanyApplication}}">
      <label for="numberInsuranceCompanyApplication">Номер заявки страховой компании</label>
</div>
  
<div class="form-floating">
 <input type="text" name="CarModel" id="CarModel"  class="form-control"  value="{{$application->CarModel}}">
      <label for="CarModel">Марка машины</label>
</div>
<div class="form-floating">
 <input type="text" name="gnz" id="gnz"  class="form-control"  value="{{$application->gnz}}">
      <label for="gnz">ГНЗ</label>
</div>
<div class="form-floating">
 <input type="text" name="vin" id="vin"  class="form-control"  value="{{$application->vin}}">
      <label for="vin">ВИН</label>
</div>
<div class="form-floating">
 <input type="text" name="ClientName" id="ClientName"  class="form-control"  value="{{$application->ClientName}}">
      <label for="ClientName">ФИО клиента</label>
</div>
<div class="form-floating">
 <input type="text" name="ClientPhone" id="ClientPhone"  class="form-control"  value="{{$application->ClientPhone}}">
      <label for="ClientPhone">Телефон клиента</label>
</div>