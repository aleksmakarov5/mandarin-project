@extends('header_menu')
@section('title')
    Заявкa 
@endsection
@section('main')   
<a href="/applications/{{$application->id}}/edit" class="nav-link">Изменить</a>
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
    <th>ВИН</th>
    <th>
      {{ $application-> vin }}
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
 {{-- @foreach ($application as $key => $value)
     <tr>
      <th>{{$key}}</th>
      <th>{{$value}}</th>
     </tr>
 @endforeach --}}
 
  {{-- <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Дата заявки</th>
      <th scope="col">Страховая компания</th>
      <th scope="col">Номер заявки </th>
      <th scope="col">Марка машины </th>
      <th scope="col">ГНЗ </th>
      <th scope="col">VIN</th>
      <th scope="col">ФИО клиента</th>
      <th scope="col">Телефон клиента</th>
      <th scope="col">Остаток времени, час</th>
      <th scope="col">Статус</th>

    </tr>
  </thead>
  <tbody>

   
 


   
<tr >
    
      <th scope="col" >
        
       -
    
      </th> 
      <th scope="col">{{date("d-m-Y ",strtotime($application->created_at))}}</th>
      <th scope="col">{{$application->name}} </th>
      <th scope="col">{{$application->numberInsuranceCompanyApplication}} </th>
      <th scope="col">{{$application->CarModel}} </th>
      <th scope="col">{{$application->gnz}}</th>
      <th scope="col">{{$application->vin}}</th>
      <th scope="col">{{$application->ClientName}}</th>
      <th scope="col">{{$application->ClientPhone}}</th>
      
      @if (((48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600)))>0)
           <th scope="col">
             {{48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600)}}
             </th>
            
                 
             @else
                <th scope="col" class="alert alert-danger">
                  Просрочено на 
             {{(48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600))*(-1)}}
             </th>
             
      @endif
       
      
      
        <th scope="col">-</th>
    </tr>
       --}}

 </tbody>
</table>
@endsection 