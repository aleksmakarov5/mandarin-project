@extends('layout')
@section('main_content')
   <a href="/applications" class="nav-link">Отмена</a>
<form action="/applications/store" method="POST">
     @csrf
    
     <div class="form-floating">
    
    <select class="form-control" id="id_insuranceCompany" name="id_insuranceCompany">
        @foreach ($InsuranceCompany as $item)
            <option value="{{$item->id}}"
@if ($item->id=== 4)
                    selected
                @endif
                >{{$item->name}}</option>
        @endforeach
      
    </select>
    <label for="id_insuranceCompany">Страховая компания</label>
  </div>

<div class="form-floating">
 <input type="text" name="numberInsuranceCompanyApplication" id="numberInsuranceCompanyApplication"  class="form-control"  >
      <label for="numberInsuranceCompanyApplication">Номер заявки страховой компании</label>
</div>
  
<div class="form-floating">
 <input type="text" name="CarModel" id="CarModel"  class="form-control"  >
      <label for="CarModel">Марка машины</label>
</div>
<div class="form-floating">
 <input type="text" name="gnz" id="gnz"  class="form-control" >
      <label for="gnz">ГНЗ</label>
</div>
<div class="form-floating">
 <input type="text" name="vin" id="vin"  class="form-control"  >
      <label for="vin">ВИН</label>
</div>
<div class="form-floating">
 <input type="text" name="ClientName" id="ClientName"  class="form-control"  >
      <label for="ClientName">ФИО клиента</label>
</div>
<div class="form-floating">
 <input type="text" name="ClientName" id="ClientName"  class="form-control"  >
      <label for="ClientName">Телефон клиента</label>
</div>
   <input type="submit" value="Создать"    class="nav-link">

</form>
@endsection