@extends('layout')
@section('main_content')
<a href="/Work/{{$id}}/doc_load" class="nav-link">Назад</a>
<h1>Проверьте данные</h1>
<form action="data_save" method="POST">
    @csrf
   <table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <tr>
  
  <tr>
    <th>Марка машины</th>
    <th>
      <input type="hidden" name="CarModel" value="{{$applications[1]}}">
      {{-- {{ $application-> CarModel }} --}}
      {{$applications[1]}}
    </th>
  </tr>
  <tr>
    <th>ГНЗ</th>
    <th>
       <input type="hidden" name="gnz" value="{{$applications[11]}}">
     
       {{$applications[11]}}
      {{-- {{ $application-> gnz }} --}}
    </th>
  </tr>
  <tr>
    <th>ВИН/Кузов</th>
    <th>
       <input type="hidden" name="vin" value="{{$applications[3]}}">
     
       {{$applications[3]}}
      {{-- {{ $application-> vin }} --}}
    </th>
  </tr>
   <tr>
    <th>ПТС/Свидетельство о регистрации</th>
    <th>
       {{$applications[7]}}
        <input type="hidden" name="vehicle_passport" value="{{$applications[7]}}">
     
      {{-- {{ $application-> vehicle_passport }} --}}
    </th>
  </tr>
   <tr>
    <th>Цвет</th>
    <th>
       {{$applications[15]}}
       <input type="hidden" name="Color" value="{{$applications[15]}}">
     {{-- {{ $application-> Color }} --}}
    </th>
  </tr>
   <tr>
    <th>Пробег</th>
    <th>
       {{$applications[5]}}
      <input type="hidden" name="mileage" value="{{$applications[5]}}">
     {{-- {{ $application-> mileage }} --}}
    </th>
  </tr>
   <tr>
    <th>Дата ДТП</th>
    <th>
       {{$applications[9]}}

       <input type="hidden" name="DTPdate" value="{{$applications[9]}}">
    {{-- {{ $application-> DTPdate}} --}}
    </th>
  </tr>
  <tr>
    <th>ФИО клиента</th>
    <th>
       {{$applications[20]}}
        <input type="hidden" name="ClientName" value="{{$applications[20]}}">
   {{-- {{ $application-> ClientName }} --}}
    </th>
  </tr>
 

  <tr>
    <th>Паспорт РФ</th>
    <th>
            <input type="hidden" name="ClientPasport" value="  @isset($applications[22])
     {{$applications[22]}}
 @endisset">
  @isset($applications[22])
     {{$applications[22]}}
 @endisset
      {{-- {{ $application->  ClientPasport }} --}}
    </th>
  </tr>
 </tbody>
</table>

 <button class="nav-link" type="submit">Сохранить</button>   

</form>


@endsection