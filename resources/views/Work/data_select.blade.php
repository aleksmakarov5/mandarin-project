@extends('layout')
@section('main_content')
<a href="/Work/{{$id}}/doc_load" class="nav-link">Назад</a>
<h1>Проверьте данные</h1>
<form action="data_load" method="POST">
    @csrf
    <input type="hidden" name="location" value="{{$location}}">
<table class="table table-striped-columns table-hover table-group-divider table-responsive">
 <thead>
  
    <tr>
      <th scope="col">Наименование</th>
      <th scope="col">Цена ед., руб. </th>
      <th scope="col">Количество нормочасов</th>
      <th scope="col">Сумма,руб</th>
    </tr>
  </thead>
  <tbody>
    @for ($i = 1; $i <  count($Works)-4; $i+=4)
    <tr>
    <td> {{ $Works[$i] }} <input type="checkbox" name='iskl[{{$i}}]' value={{$i}}> </td>
     <td> {{ $Works[$i+1] }}<input type="checkbox" name='iskl[{{$i+1}}]'value={{$i+1}}> </td>
      <td> {{ $Works[$i+2] }}<input type="checkbox" name='iskl[{{$i+2}}]'value={{$i+2}}> </td>
       <td> {{ $Works[$i+3] }}<input type="checkbox" name='iskl[{{$i+3}}]'value={{$i+3}}> </td>
        
            </tr>
    @endfor 
    </tbody>
</table>
@if ($reset>0)
<input type="hidden" name="reset" value=1>
  <button class="nav-link" type="submit">Сброс</button>
@else
 <button class="nav-link" type="submit">Удалить выбранные ячейки</button>   
@endif
</form>
<form action="data_save" method="post">
    @csrf
    <input type="hidden" name="id_application" value="{{$id}}">
 @for ($i = 1,$j=1; $i <  count($Works)-4; $i+=4,$j++)
    <input type="hidden" name="name[{{$j}}]" value="{{$Works[$i]}}">
    <input type="hidden" name="price[{{$j}}]" value="{{$Works[$i+1]}}">
    <input type="hidden" name="count_hours[{{$j}}]" value="{{$Works[$i+2]}}">
    <input type="hidden" name="summ[{{$j}}]" value="{{$Works[$i+3]}}">
     @endfor 
    <button class="nav-link" type="submit">Сохранить</button>
</form>

@endsection