@extends('admin.header_menu')
@section('title')
  Пользователи 
@endsection
@section('main') 
<a href="/users/create" class="nav-link">Добавить пользователя</a>

<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">ФИО</th>
      <th scope="col">email</th>
      <th scope="col">Номер телефона</th>
      <th scope="col">Группа пользователя</th>
      <th scope="col"></th>

    </tr>
  </thead>
  <tbody>

   
 
@foreach ($users->all() as $user)
<tr >
    
      <th scope="col" >
        
        {{$user->id}}
    
      </th> 
      <th scope="col" onclick="window.location.href='/user/{{$user->id}}'; return false">{{$user->name}} </th>
      <th scope="col" onclick="window.location.href='/user/{{$user->id}}'; return false">{{$user->email}} </th>
      <th scope="col" onclick="window.location.href='/user/{{$user->id}}'; return false">{{$user->phone}} </th>
      <th scope="col" onclick="window.location.href='/user/{{$user->id}}'; return false">{{$user->GroupName}} </th>
    <th scope="col">
         <a onclick="if (!confirm('Точно удалить пользователя <{{$user->name}}?>')) return false" href="{{route('user.destroy', $user->id)}}" class="nav-link">Удалить</a>
     
        </th>
    </tr>
      
@endforeach
 </tbody>
</table>
{{ $users->links() }}
 @endsection 