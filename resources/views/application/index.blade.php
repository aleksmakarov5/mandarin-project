@extends('header_menu')
@section('title')
    Заявкa 
@endsection
@section('main')   
@can('admin')
<a href="/application/{{$id}}/doc_load" class="nav-link">Загрузить данные из документа</a>
<br>  
<a href="/applications/{{$application->id}}/edit" class="nav-link">Изменить</a> 
   <br>
 <a href="/applications/{{$application->id}}/send_mail" class="nav-link">Отправить уведомление</a> 
   

@endcan
<table class="table table-striped-columns table-hover table-group-divider table-responsive">
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
      {{ $application->name}}
    </th>
  </tr>
  <tr>
    <th>№ заявки страховой компании</th>
    <th>
      {{ $application-> numberInsuranceCompanyApplication }}
    </th>
  </tr>
  <tr>
    <th>Марка машины</th>
    <th>
      {{ $application-> CarModel }}
    </th>
  </tr>
  <tr>
    <th>ГНЗ</th>
    <th>
      {{ $application-> gnz }}
    </th>
  </tr>
  <tr>
    <th>ВИН/Кузов</th>
    <th>
      {{ $application-> vin }}
    </th>
  </tr>
   <tr>
    <th>ПТС/Свидетельство о регистрации</th>
    <th>
      {{ $application-> vehicle_passport }}
    </th>
  </tr>
   <tr>
    <th>Цвет</th>
    <th>
      {{ $application-> Color }}
    </th>
  </tr>
   <tr>
    <th>Пробег</th>
    <th>
      {{ $application-> mileage }}
    </th>
  </tr>
   <tr>
    <th>Дата ДТП</th>
    <th>
      {{ $application-> DTPdate}}
    </th>
  </tr>
  <tr>
    <th>ФИО клиента</th>
    <th>
      {{ $application-> ClientName }}
    </th>
  </tr>
  <tr>
    <th>Номер телефона клиента</th>
    <th>
      {{ $application->  ClientPhone }}
    </th>
  </tr>

  <tr>
    <th>Паспорт РФ</th>
    <th>
      {{ $application->  ClientPasport }}
    </th>
  </tr>
    <tr>
    <th>Менеджер по работе с клиентами</th>
    <th>
      {{ ($mrk) }}
    </th>
  </tr>
     <tr>
    <th>Запчастист</th>
    <th>
      {{ $spm}}
    </th>
  </tr>
 </tbody>
</table>
@endsection 