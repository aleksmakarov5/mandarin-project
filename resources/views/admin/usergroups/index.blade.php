@extends('admin.header_menu')
@section('title')
  Пользователи 
@endsection
@section('main') 
<a href="/usergroups/create" class="nav-link">Добавить группу</a>

<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Имя группы</th>
      <th scope="col">GroupDescription</th>
       <th scope="col"></th>

    </tr>
  </thead>
  <tbody>

   
 
@foreach ($usergroups->all() as $usergroup)
<tr >
    
      <th scope="col" >
        
        {{$usergroup->id}}
    
      </th> 
      <th scope="col" onclick="window.location.href='/usergroup/{{$usergroup->id}}'; return false">{{$usergroup->GroupName}} </th>
      <th scope="col" onclick="window.location.href='/usergroup/{{$usergroup->id}}'; return false">{{$usergroup->GroupDescription}} </th>
     <th scope="col">
         <a onclick="if (!confirm('Точно удалить группу <{{$usergroup->GroupName}}?>')) return false" href="{{route('usergroup.destroy', $usergroup->id)}}" class="nav-link">Удалить</a>
     
        </th>
    </tr>
      
@endforeach
 </tbody>
</table>
 @endsection 