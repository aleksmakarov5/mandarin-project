@extends('header_menu')
@section('title')
    Работы
@endsection
@section('main')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="row">


        <div class="col">
            <table class="editable" id='demo'>
                <thead>
                    <tr>
                        <th scope="col" colspan=4>Фактические работы</th>



                    </tr>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Наименование</th>

                        {{-- <th scope="col">Сумма</th> --}}
                        <th scope="col">Цена сервиса</th>
                        <th scope="col">
                            @can('company')
                                Разница стоимости
                            @endcan
                        </th>

                    </tr>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Итого</th>
                        {{-- <th scope="col">{{$summ_ic}}</th> --}}
                        <th scope="col">{{ $summ_service }}</th>
                        <th scope="col">
                            @can('company')
                                <div
                                    class="
       @if ($delta < 0) alert alert-success
       @else
       alert alert-danger @endif
       ">

                                    {{ $delta }}</div>
                            @endcan
                        </th>

                    </tr>
                </thead>
                <tbody>



                    @foreach ($Works_serv as $key => $Work)
                        <tr @if ($Work->author == 9) class="sto" @endif>
                            <form name="form-{{ $key }}" id='form-{{ $key }}'
                                action="/Work/{{ $Work->id }}/update" method="POST">

                                @csrf
                                <input type="hidden" name="id_application" value='{{ $id }}'>

                                <td scope="col" id="td_id--{{ $key }}">
                                    {{ $key + 1 }}
                                    <a name="{{ $Work->id }}"></a>
                                </td>

                                <input type="hidden" name="id" id='id--{{ $key }}'
                                    value=' {{ $Work->id }}'>
                                <td scope="col" id="td_name--{{ $key }}">{{ $Work->name }}</td>
                                <input type="hidden" name="name" id='name--{{ $key }}'
                                    value='{{ $Work->name }}'>

                                <td id="td_price_service--{{ $key }}" scope="col">{{ $Work->price_service }}
                                </td>
                                <input type="hidden" name="price_service" id='price_service--{{ $key }}'
                                    value='{{ $Work->price_service }}'>
                                <input type="hidden" name="summ" id='summ--{{ $key }}'
                                    value='{{ $Work->summ }}'>

                                <td scope="col"> <a
                                        onclick="if (!confirm('Точно удалить работу <{{ $Work->name }}?>')) return false"
                                        href="{{ route('Work.destroy', $Work->id) }}" class="nav-link"><img
                                            src="/storage/img/delete.png" width="20" alt="delete"></a>
                                </td>
                        </tr>
                        </form>
                    @endforeach
                    <tr>

                        <form action="/Works/store" method="POST">
                            @csrf
                            <th>
                                <input name = "id_application" type="hidden" value="{{ $id }}">
                            </th>
                            <th>
                                <div class="form-floating">

                                    <input type="text" name="name" id="name" class="form-control">

                                </div>

                                <div class="form-floating">
                                    <input type="hidden" name="summ" id="summ" class="form-control" value=0>

                                </div>
                            </th>
                            <th>
                                <div class="form-floating">
                                    <input type="text" name="price_service" id="price_service" class="form-control">

                                    <input name = "author" type="hidden" value="{{ Auth::user()->GroupId }}">
                                </div>
                            </th>
                            <th>
                                <input type="submit" value="Создать" class="nav-link">
                            </th>
                        </form>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col">
            @can('company')
                <table class="editable" id='demo'>
                    <thead class="accordion">
                        <tr>
                            <th scope="col" colspan=4>Данные из калькуляции</th>
                            <th scope="col" colspan=4>
                                <a href="/Work/{{ $id }}/doc_load" class="nav-link">Загрузить</a>
                            </th>



                        </tr>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Наименование</th>

                            <th scope="col">Цена страховой</th>
                            <th scope="col">Цена сервиса</th>
                            {{-- <th scope="col">Цена сервиса</th> --}}
                            <th scope="col"></th>

                        </tr>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Итого</th>
                            <th scope="col">{{ $summ_ic }}</th>
                            {{-- <th scope="col">{{$summ_service}}</th> --}}
                            <th scope="col"></th>
                            <th scope="col"></th>

                        </tr>
                    </thead>
                    <tbody>



                        @foreach ($Works as $key => $Work)
                            <tr>
                                <form name="form{{ $key }}" id='form{{ $key }}'
                                    action="/Work/{{ $Work->id }}/update" method="POST">

                                    @csrf
                                    <input type="hidden" name="id_application" value='{{ $id }}'>

                                    <td scope="col" id="td_id-{{ $key }}">
                                        {{ $key + 1 }}
                                        <a name="{{ $Work->id }}"></a>
                                    </td>

                                    <input type="hidden" name="id" id='id-{{ $key }}'
                                        value=' {{ $Work->id }}'>
                                    <td scope="col" id="td_name-{{ $key }}">{{ $Work->name }}</td>
                                    <input type="hidden" name="name" id='name-{{ $key }}'
                                        value='{{ $Work->name }}'>

                                    <td scope="col" id="td_summ-{{ $key }}">{{ $Work->summ }}</td>

                                    <input type="hidden" name="summ" id='summ-{{ $key }}'
                                        value='{{ $Work->summ }}'>
                                    <td scope="col" id="td_price_service-{{ $key }}">{{ $Work->price_service }}
                                    </td>
                                    <input type="hidden" name="price_service" id='price_service-{{ $key }}'
                                        value='{{ $Work->price_service }}'>
                                    <td scope="col"> <a
                                            onclick="if (!confirm('Точно удалить работу <{{ $Work->name }}?>')) return false"
                                            href="{{ route('Work.destroy', $Work->id) }}" class="nav-link"><img
                                                src="/storage/img/delete.png" width="20" alt="delete"></a>
                                    </td>
                            </tr>
                            </form>
                        @endforeach
                        <tr>

                            <form action="/Works/store" method="POST">
                                @csrf
                                <th>
                                    <input name = "id_application" type="hidden" value="{{ $id }}">
                                </th>
                                <th>
                                    <div class="form-floating">

                                        <input type="text" name="name" id="name" class="form-control">

                                    </div>
                                </th>
                                <th>

                                    <div class="form-floating">
                                        <input type="text" name="summ" id="summ" class="form-control" value=0>

                                    </div>

                                </th>
                                <th>
                                    <input type="submit" value="Создать" class="nav-link">
                                </th>
                            </form>
                        </tr>
                    </tbody>
                </table>
            @endcan
        </div>
    </div>


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
                    let str = cell.id;
                    let start = str.indexOf("_");
                    let isid = document.getElementById(str.substring(start + 1, str.length));
                    start = str.indexOf("-");
                    let id = str.substring(start + 1, str.length);
                    isid.value = cell.innerHTML;
                    let form = document.getElementById('form' + id);
                    let params = new FormData(form);
                    let url = form.getAttribute('action');

                    fetch(url, {
                        method: 'POST',
                        body: params
                    });
                }
            }
        };
    </script>

@endsection
