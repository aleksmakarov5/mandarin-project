@extends('header_menu')
@section('title')
    Скрытые повреждения
@endsection
@section('main')
    <a href="/export_hda/{{ $id }}/11" class="nav-link">Создать печатную форму</a>
    @if (session('GroupId') != 6 && session('GroupId') != 5)
        <a href="/answer_ic_mrk/{{ $id }}" class="nav-link">Отправить в страховую компанию</a>
    @endif


    {{-- 
    <table class="editable" id="demo">
        <thead>

            <tr>
                <th scope="col">#</th>
                <th scope="col">Наименование детали автомобиля</th>
                <th scope="col">Повреждение</th>
                <th scope="col">Требуемое ремонтное воздействие</th>
                <th scope="col"></th>


        </thead>
        <tbody>



            @foreach ($troubleshootings as $key => $troubleshooting)
                <form name="form{{ $key }}" id='form{{ $key }}'
                    action="/troubleshooting/{{ $troubleshooting->id }}/update" method="POST">
                    @csrf
                    <input type="hidden" name="id_application" value='{{ $id }}'>
                    <tr>

                        <td scope="col" id="td_id-{{ $key }}">

                            {{ $key + 1 }}

                        </td>
                        <input type="hidden" name="id" id='id-{{ $key }}'
                            value=' {{ $troubleshooting->id }}'>
                        <td id="td_PartName-{{ $key }}">
                            {{ $troubleshooting->PartName }}</td>
                        <input type="hidden" name="PartName" id='PartName-{{ $key }}'
                            value=' {{ $troubleshooting->PartName }}'>
                        <td id="td_Сonclusion-{{ $key }}">
                            {{ $troubleshooting->Сonclusion }}</td>
                        <input type="hidden" name="Сonclusion" id='Сonclusion-{{ $key }}'
                            value=' {{ $troubleshooting->Сonclusion }}'>

                        <td id="td_Work-{{ $key }}">
                            {{ $troubleshooting->Work }}</td>
                        <input type="hidden" name="Work" id='Work-{{ $key }}'
                            value=' {{ $troubleshooting->Work }}'>
                        <td scope="col"> <a
                                onclick="if (!confirm('Точно удалить позицию <{{ $troubleshooting->PartName }}?>')) return false"
                                href="{{ route('troubleshooting.destroy', $troubleshooting->id) }}" class="nav-link"><img
                                    src="/storage/img/delete.png" width="30" alt="delete"></a>
                        </td>
                    </tr>
                </form>
            @endforeach
            <tr>

                <form action="/troubleshootings/store" method="POST">
                    @csrf
                    <th>
                        <input name = "id_application" type="hidden" value="{{ $id }}">
                    </th>
                    <th>
                        <div class="form-floating @error('PartName') alert alert-danger @enderror">

                            <input type="text" name="PartName" id="PartName" class="form-control"
                                value="{{ old('PartName') }}">

                        </div>
                    </th>
                    <th>

                        <div class="form-floating @error('Сonclusion') alert alert-danger @enderror">
                            <input type="text" name="Сonclusion" id="Сonclusion" class="form-control"
                                value="{{ old('Сonclusion') }}">

                        </div>

                    </th>
                    <th>

                        <div class="form-floating @error('Work') alert alert-danger @enderror">
                            <input type="text" name="Work" id="Work" class="form-control"
                                value="{{ old('Work') }}">

                        </div>

                    </th>


                    <th>
                        <input type="submit" value="Создать" class="nav-link">
                    </th>
                </form>
            </tr>
        </tbody>
    </table>
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            </ul>
        </div>
    @endif --}}

    <div id="app">
        <troubleshootings :app_id={{ $id }}>

    </div>
    {{-- <script>
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
    </script> --}}
@endsection
