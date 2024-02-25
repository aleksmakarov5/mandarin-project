@extends('layout')
@section('title')
    Заявки
@endsection
@section('main_content')   
<h1>Все заявки</h1> 
@can('admin')
    <a href="/applications/create" class="nav-link">Добавить заявку</a>
@endcan
<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
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
      <th scope="col"></th>

    </tr>
  </thead>
  <tbody>

   
 
@foreach ($applications->all() as $application)
<tr >
    
      <th scope="col" >
        
        {{$application->id}}
    
      </th> 
      <th scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{date("d-m-Y h:m",strtotime($application->created_at))}}</th>
      <th scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->name}} </th>
      <th scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->numberInsuranceCompanyApplication}} </th>
      <th scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->CarModel}} </th>
      <th scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->gnz}}</th>
      <th scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->vin}}</th>
      <th scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->ClientName}}</th>
      <th scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->ClientPhone}}</th>
    @if (((48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600)))>0)
           <th scope="col"  onclick="window.location.href='/application/{{$application->id}}'; return false">
             {{48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600)}}
             </th>
            
                 
             @else
                <th scope="col" class="alert alert-danger"  onclick="window.location.href='/application/{{$application->id}}'; return false">
                  Просрочено на 
             {{(48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600))*(-1)}}
             </th>
             
      @endif
        <th scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">-</th>
        <th scope="col">
          <a href="{{route('applications.destroy', $application->id)}}" class="nav-link"><img src="/storage/img/delete.png" width="30" alt="delete"></a>
        </th>
    </tr>
      
@endforeach
 </tbody>
</table>
@endsection 