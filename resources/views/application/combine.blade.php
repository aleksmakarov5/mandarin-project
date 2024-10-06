@extends('layout')
@section('title')
    Все Ящики
@endsection
@section('main_content')
    {{-- <a href="/back" class="nav-link">Назад</a> --}}
    <center>
        <table id="applications">
            <thead>


                <tr>

                    <th scope="col"><input class="form-control" type="text" placeholder="№" id="search-text0"
                            onkeyup="tableSearch(0,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="ГРЗ" id="search-text1"
                            onkeyup="tableSearch(1,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="Марка авто" id="search-text2"
                            onkeyup="tableSearch(2,'applications')"></th>
                    <th scope="col"><input class="form-control" type="text" placeholder="№ убытка" id="search-text3"
                            onkeyup="tableSearch(3,'applications')"></th>

            </thead>
            <tbody>
                @foreach ($applications as $application)
                    <tr>
                        <td
                            onclick="window.location.href='/combine_fin/{{ $id }}/{{ $application->id }}'; return false">
                            <div class="mail_index">{{ $application->id }}</div>
                        </td>
                        <td
                            onclick="window.location.href='/combine_fin/{{ $id }}/{{ $application->id }}'; return false">
                            <div class="mail_index">{{ $application->gnz }}</div>
                        </td>
                        <td
                            onclick="window.location.href='/combine_fin/{{ $id }}/{{ $application->id }}'; return false">
                            <div class="mail_index">{{ $application->CarModel }}</div>
                        </td>
                        <td
                            onclick="window.location.href='/combine_fin/{{ $id }}/{{ $application->id }}'; return false">
                            <div class="mail_index">{{ $application->numberInsuranceCompanyApplication }}</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </center>
    <script>
        function tableSearch(k, table) {
            var phrase = document.getElementById('search-text' + k);
            var table = document.getElementById(table);
            var regPhrase = new RegExp(phrase.value, 'i');
            var flag = false;
            for (var i = 1; i < table.rows.length; i++) {
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
                cell.onkeyup = (evt) => {
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
