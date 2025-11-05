@extends('layout')
@section('title')
    Все Ящики
@endsection
@section('main_content')
    {{ $messages->links('vendor.pagination.bootstrap-4') }}
    </div>
    <div class="mailbox">
        <div class="message-list">
            <a href="#" class="msg_but"><img src="/storage/img/new_mail.png" width="20" alt="Ответить"></a>
            <table id ="messages">
                <thead>


                    <tr>

                        <th scope="col"><input class="form-control" type="text" placeholder="поиск" id="search-text0"
                                onkeyup="tableSearch(0,'messages')"></th>


                </thead>
                <tbody>
                    @foreach ($messages as $message)
                        <tr>

                            <td
                                onclick="window.location.href='/mailbox/{{ $type }}/{{ $company_id }}/{{ $message->id }}#{{ $message->id }}'; return false">
                                <div id="{{ $message->id }}"
                                    class="@if ($message->id == $message_id) current @else


                                    message-name @endif
                                    @if (!$message->is_ready) big-text @endif

                                    @if ($message->type) blue
                            @else
                                @if (isset($message->application_id)) green @endif
                            @endif
                                    ">

                                    <div class="details">
                                        <p class="msg-subject">
                                            {{ $message->subject }}
                                        </p>
                                        <p class="sender-email">
                                            <i class="far fa-clock"> От кого: </i>
                                            @if (isset($message->from_address))
                                                {{ $message->from_address }}
                                            @endif
                                        </p>
                                        <p class=""><i class="far fa-clock">
                                                @if (isset($message->message_date))
                                                    {{ Date::parse($message->message_date)->format('j F Yг. H:i') }}
                                                @endif
                                            </i>
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
            </table>

        </div>
        <div class="message-show">
            @if (isset($mail->subject))
                <div class="message-header">
                    <h2>{{ $mail->subject }}</h2>
                    <p class="sender-email"><b>От кого:</b>
                        @if (isset($mail->from->email))
                            {{ $mail->from->email }}
                        @endif
                    </p>
                    <p class="sender-email"><b>Кому:</b>
                        @if (isset($mail->to[0]->email))
                            @foreach ($mail->to as $to)
                                {{ $to->email }};
                            @endforeach

                        @endif
                    </p>
                    <p class=""><i class="far fa-clock">
                            @if (isset($mail->message_date))
                                {{ Date::parse($mail->message_date)->format('j F Yг. H:i') }}
                            @endif
                        </i>
                    </p>
                    <div class="message-button">
                        <a href="#" class="msg_but"><img src="/storage/img/reply.png" width="20"
                                alt="Ответить"></a>
                        <a href="#" class="msg_but"><img src="/storage/img/forward.png" width="20"
                                alt="Переслать"></a>
                        <a onclick="if (!confirm('Точно удалить письмо {{ $mail->subject }}?')) return false"
                            href="/mail_delete/{{ $mail->id }}" class="msg_but"><img src="/storage/img/delete.png"
                                width="20" alt="Удалить"></a>
                        @if (isset($mail->application_id))
                            <a href="/application/{{ $mail->application_id }}" class="msg_but"><img
                                    src="/storage/img/open.png" width="20" alt="Открыть"></a>
                        @else
                            <a href="/applications/add_mail/{{ $mail->mailbox_id }}/{{ $id }}/{{ $mail->id }}"
                                class="msg_but"><img src="/storage/img/add.png" width="20" alt="Добавить"></a>
                        @endif

                    </div>

                </div>
            @endif
            <div>
                @php
                    echo $body;
                @endphp
            </div>
            @if (isset($mail->attachment))
                <div class="attachments-sections">

                    @foreach ($mail->attachment as $attachment)
                        @if (pathinfo($attachment->location, PATHINFO_EXTENSION) != 'gif')
                            <div class="attachments-item">


                                <p class="file-name"><a
                                        @if (isset($attachment->id_application)) href="/storage/files/{{ $attachment->id_application }}/{{ $attachment->location }}" @else href="/storage/files/temp/{{ $attachment->location }}" @endif"
                                        target="_blank" class="view">
                                        <img src="/storage/img/{{ mb_substr($attachment->location, mb_strripos($attachment->location, '.') + 1, mb_strlen($attachment->location) - mb_strripos($attachment->location, '.')) }}.png"
                                            alt="{{ mb_substr($attachment->mime_type, mb_strpos($attachment->mime_type, '/'), strlen($attachment->mime_type) - mb_strpos($attachment->mime_type, '/')) }}"
                                            width=25px>

                                        {{ $attachment->file_name }} </a></p>

                                <i class="file-size">
                                    {{ round($attachment->file_size / 1024 / 1024, 2) }} мб</i>




                            </div>
                        @endif
                    @endforeach

                </div>
            @endif
        </div>
    </div>
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
