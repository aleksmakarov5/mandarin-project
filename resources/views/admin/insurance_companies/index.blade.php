@extends('admin.header_menu')
@section('title')
  Страховые компании
@endsection
@section('main') 
<a href="/insurance_companies/create" class="nav-link">Добавить компнаию</a>

<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Название</th>
      <th scope="col">Менеджер</th>
      <th scope="col">Теелфон</th>
      <th scope="col">Email</th>
       <th scope="col"></th>

    </tr>
  </thead>
  <tbody>

   
 
@foreach ($insurance_companies->all() as $insurance_company)
<tr >
    
      <th scope="col" >
        
        {{$insurance_company->id}}
    
      </th> 
      <th scope="col" onclick="window.location.href='/insurance_company/{{$insurance_company->id}}'; return false">{{$insurance_company->name}} </th>
        <th scope="col" onclick="window.location.href='/insurance_company/{{$insurance_company->id}}'; return false">{{$insurance_company->manager}} </th>
      <th scope="col" onclick="window.location.href='/insurance_company/{{$insurance_company->id}}'; return false">{{$insurance_company->phone}} </th>
      <th scope="col" onclick="window.location.href='/insurance_company/{{$insurance_company->id}}'; return false">{{$insurance_company->email}} </th>
    
     <th scope="col">
         <a onclick="if (!confirm('Точно удалить компанию <{{$insurance_company->name}}?>')) return false" href="{{route('insurance_company.destroy', $insurance_company->id)}}" class="nav-link">Удалить</a>
     
        </th>
    </tr>
      
@endforeach
 </tbody>
</table>
 @endsection 