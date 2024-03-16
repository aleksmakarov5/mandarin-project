@extends('layout')
@section('title')
    Заявки
@endsection
@section('main_content')   
<h1>Все заявки</h1> 
@can('admin')
    <a href="/applications/create" class="nav-link">Добавить заявку</a>
@endcan
<table id ="applications" class="editable">
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
      <tr>
      <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text0" onkeyup="tableSearch(0,'applications')">
</th>
      <th scope="col"><input class="form-control" type="text" placeholder="Дата заявки" id="search-text1" onkeyup="tableSearch(1,'applications')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="Страховая компания" id="search-text2" onkeyup="tableSearch(2,'applications')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="Номер заявки " id="search-text3" onkeyup="tableSearch(3,'applications')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="Марка машины " id="search-text4" onkeyup="tableSearch(4,'applications')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="ГНЗ" id="search-text5" onkeyup="tableSearch(5,'applications')"> </th>
      <th scope="col"><input class="form-control" type="text" placeholder="VIN" id="search-text6" onkeyup="tableSearch(6,'applications')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="ФИО клиента" id="search-text7" onkeyup="tableSearch(7,'applications')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="Телефон клиента" id="search-text8" onkeyup="tableSearch(8,'applications')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="Остаток времени, час" id="search-text9" onkeyup="tableSearch(9,'applications')"></th>
      <th scope="col"><input class="form-control" type="text" placeholder="Статус" id="search-text10" onkeyup="tableSearch(10,'applications')"></th>
      <th scope="col"></th>

    </tr>
  </thead>
  <tbody>

   
 
@foreach ($applications->all() as $application)

<tr  @if (Auth::user()->GroupId!=$application->current_group) class="grey" @endif>
   
      <td>
        
        {{$application->id}}
    
      </td> 
      <td  scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false" >{{date("d-m-Y",strtotime($application->created_at))}}</td>
      <td scope="col" >{{$application->name}} </td>
      <td scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->numberInsuranceCompanyApplication}} </td>
      <td scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->CarModel}} </td>
      <td scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->gnz}}</td>
      <td scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->vin}}</td>
      <td  onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->ClientName}}</td>
      <td  onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->ClientPhone}}</td>
    @if (((48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600)))>0)
           <td scope="col"  onclick="window.location.href='/application/{{$application->id}}'; return false">
<div class="progress">
  <div class="progress-bar" style="width: {{round(48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600))/48*100}}%;" role="progressbar" aria-valuenow="{{48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600)}}" aria-valuemin="0" aria-valuemax="48">
  {{48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600)}} часов
  </div>
</div>

             
             </td>
            
                 
             @else
                <td scope="col" class="alert alert-danger"  onclick="window.location.href='/application/{{$application->id}}'; return false">
                  Просрочено на 
             {{(48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600))*(-1)}} часов
             </td>
             
      @endif
        <td scope="col" onclick="window.location.href='/application/{{$application->id}}'; return false">{{$application->StatusName}}</td>
        <td scope="col">
          <a href="{{route('applications.destroy', $application->id)}}" class="nav-link"><img src="/storage/img/delete.png" width="30" alt="delete"></a>
        </td>
      
    </tr>
      
@endforeach
 </tbody>
</table>
 <script>
        function tableSearch(k, table) {
            var phrase = document.getElementById('search-text' + k);
            var table = document.getElementById(table);
            var regPhrase = new RegExp(phrase.value, 'i');
            var flag = false;
            for (var i = 3; i < table.rows.length; i++) {
                flag = false;
                flag = regPhrase.test(table.rows[i].cells[k].innerHTML);
                if (flag) {
                    table.rows[i].style.display = "";
                } else {
                    table.rows[i].style.display = "none";
                }

            }
        }
        // ИНИЦИАЛИЗАЦИЯ - ДВОЙНОЙ ЩЕЛЧОК ДЛЯ РЕДАКТИРОВАНИЯ ЯЧЕЙКИ
        window.addEventListener("DOMContentLoaded", () => {
            for (let cell of document.querySelectorAll("#demo td")) {
                cell.ondblclick = () => {
                    editable.edit(cell);
                };
            }
        });
        let editable = {
            // "ПРЕОБРАЗОВАТЬ" В РЕДАКТИРОВАННУЮ ЯЧЕЙКУ
            edit: (cell) => {
                // УДАЛИТЬ "ДВОЙНОЙ ЩЕЛЧОК ДЛЯ РЕДАКТИРОВАНИЯ"
                cell.ondblclick = "";
                // РЕДАКТИРУЕМОЕ СОДЕРЖИМОЕ
                cell.contentEditable = true;
                cell.focus();
                // "ОТМЕТИТЬ" ТЕКУЩУЮ ВЫБРАННУЮ ЯЧЕЙКУ
                cell.classList.add("edit");
                editable.selected = cell;
                // НАЖМИТЕ ENTER ИЛИ ЩЕЛКНИТЕ ВНЕ, ЧТОБЫ ЗАВЕРШИТЬ РЕДАКТИРОВАНИЕ
                window.addEventListener("click", editable.close);
                cell.onkeydown = (evt) => {
                    if (evt.key == "Enter") {
                        editable.close(true);
                        return false;
                    }
                };
            },
            // ЗАВЕРШИТЬ "РЕЖИМ РЕДАКТИРОВАНИЯ"
            selected: null, // текущая выбранная ячейка
            close: (evt) => {
                if (evt.target != editable.selected) {

                    // УДАЛИТЬ "РЕДАКТИРОВАНИЕ"
                    window.getSelection().removeAllRanges();
                    editable.selected.contentEditable = false;
                    // ВОССТАНОВИТЬ КЛИК-СЛУШАТЕЛИ
                    window.removeEventListener("click", editable.close);

                    let cell = editable.selected;
                    cell.ondblclick = () => {
                        editable.edit(cell);
                    };
                    // "СНЯТЬ ПОМЕТКУ" ТЕКУЩЕЙ ВЫБРАННОЙ ЯЧЕЙКИ
                    editable.selected.classList.remove("edit");
                    editable.selected = null;
                    // newvar = "Третий способ вывода переменной";
                    let str = cell.id;
                    let start = str.indexOf("_");
                    let isid = document.getElementById(str.substring(start + 1, str.length));
                    start = str.indexOf("-");
                    let id = str.substring(start + 1, str.length);
                    isid.value = cell.innerHTML;
                    let form = document.getElementById('form' + id);
                    form.submit();
                }
            }
        };
    </script>
@endsection 