@extends('layout')
@section('title')
    Все Ящики
@endsection
@section('main_content')
    <div class="content-wrapper">
        <div class="email-wrapper wrapper">

            <div class="row align-items-stretch">
                @foreach ($messages as $message)
                    <button class="accordion">
                        <div class="sender-details">
                            <div class="details">
                                <p class="msg-subject">
                                    {{ $message->subject }}
                                </p>
                                <p class="sender-email">
                                    <i class="far fa-clock"> От кого: </i>
                                    @if (isset($message->from->email))
                                        {{ $message->from->email }}
                                    @endif
                                </p>
                                <p class=" small mb-0 text-white"><i class="far fa-clock">
                                        @if (isset($message->message_date))
                                            {{ Date::parse($message->message_date)->format('j F Yг. H:i') }}
                                        @endif
                                    </i>
                                </p>
                            </div>
                        </div>
                    </button>
                    <div class="panel">

                        <p>

                        <div class="mail-view">
                            <div class="row">
                                <div class="col">
                                    <div class="btn-toolbar">

                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i
                                                    class="mdi mdi-share text-primary mr-1"></i>
                                                </i><a class="mdi mdi-delete text-primary mr-1"
                                                    href="/mail_delete/{{ $message->id }}">Удалить</a></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="message-body">

                                <div class="message-content">
                                    @php
                                        $body = $message->body_html;
                                        $body_start = mb_strpos($body, '<body');
                                        $body_end = mb_strpos($body, '</body>');
                                        if ($body_start) {
                                            $body = mb_substr($body, $body_start, $body_end - $body_start);
                                        }
                                        $body = str_replace('color', '', $body);

                                        $body = str_replace('font-size:', '', $body);
                                        $body = str_replace('Отправлено из мобильной Почты Mail.ru', '', $body);
                                        $body = str_replace('\n', '<br>', $body);
                                        echo $body;

                                    @endphp

                                </div>
                                <div class="attachments-sections">
                                    <ul>
                                        @foreach ($message->attachment as $attachment)
                                            <li>
                                                <div class="thumb"><i class="mdi mdi-file-pdf"></i></div>
                                                <div class="details">
                                                    <p class="file-name"><a
                                                            href="/storage/files/{{ $attachment->id_application }}/{{ $attachment->location }}"
                                                            class="view" target="_blank">
                                                            <img src="/storage/img/{{ mb_substr($attachment->location, mb_strripos($attachment->location, '.') + 1, mb_strlen($attachment->location) - mb_strripos($attachment->location, '.')) }}.png"
                                                                alt="{{ mb_substr($attachment->mime_type, mb_strpos($attachment->mime_type, '/'), strlen($attachment->mime_type) - mb_strpos($attachment->mime_type, '/')) }}"
                                                                width=25px>

                                                            {{ $attachment->file_name }} </a></p>
                                                    <div class="buttons">
                                                        <p class="file-size">
                                                            {{ round($attachment->file_size / 1024 / 1024, 2) }} мб</p>

                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        function tableSearch(k, table) {
            var phrase = document.getElementById('search-text' + k);
            var table = document.getElementById(table);
            var regPhrase = new RegExp(phrase.value, 'i');
            var flag = false;
            for (var i = 2; i < table.rows.length; i++) {
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
