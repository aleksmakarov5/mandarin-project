@extends('header_menu')
@section('title')
    Заявкa
@endsection
@section('main')
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
                                <p class="text-muted small mb-0"><i class="far fa-clock">
                                        @if (isset($message->message_date))
                                            {{ date('d-m-Y h:m', strtotime($message->message_date)) }}
                                        @endif
                                    </i>
                                </p>
                            </div>
                        </div>
                    </button>
                    <div class="panel">
                        Кому: @foreach ($message->to as $to)
                            <p class="text-muted small mb-0">
                                <i class="far fa-clock">
                                    @if (isset($to->email))
                                        {{ $to->email }};
                                    @endif
                                </i>
                            </p>
                        @endforeach
                        <p>

                        <div class="mail-view">
                            <div class="row">
                                <div class="col">
                                    <div class="btn-toolbar">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i
                                                    class="mdi mdi-reply text-primary mr-1"></i> Ответить</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i
                                                    class="mdi mdi-reply-all text-primary mr-1"></i>Ответить всем</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i
                                                    class="mdi mdi-share text-primary mr-1"></i>Переслать</button>
                                        </div>
                                        <div class="btn-group">

                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i
                                                    class="mdi mdi-delete text-primary mr-1"></i>Удалить</button>
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
    @can('admin')
        <div class="row">

            <div class="col">

                <ul class="list-unstyled">


                    <form action="/answer_ic_sch_sent/{{ $id }}" method="post">
                        @csrf
                        <div class="form-float">
                            <input type="text" name="to" id="to" class="form-cont" value='{{ $to_email }}'>
                            <label for="to">Получатель</label>
                        </div>
                        <input type="hidden" name="from" value="{{ Auth::user()->id }}">
                        <input type="hidden" name="subject"
                            value="@if (isset($subject)) {{ $subject }} @else ---- @endif">
                        <li class="bg-white mb-3">

                            <div class="form-outline">
                                <textarea class="form-cont" id="textAreaExample2" rows="15" name="message">

              </textarea>

                            </div>

                            <div class="row">
                                @foreach ($attach as $key => $doc)
                                    <div class="card" style="width: 20rem;">
                                        <div class="row g-0">
                                            <div class="col-md-4">
                                                <form name="form{{ $key }}" action="data_load" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="location" value='{{ $doc->location }}'>
                                                    <img src="/storage/img/{{ pathinfo($doc->location, PATHINFO_EXTENSION) }}.png"
                                                        class="card-img-top"
                                                        alt="{{ mb_substr($doc->mime_type, mb_strpos($doc->mime_type, '/'), strlen($doc->mime_type) - mb_strpos($doc->mime_type, '/')) }}">
                                                </form>
                                            </div>

                                            <div class="col-md-8">

                                                <div class="card-body">

                                                    <h5 class="card-title">{{ $doc->file_name }}</h5>
                                                    <input type="checkbox" name="location[]" value='{{ $doc->location }}'>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                            <input type="submit" class="nav-link">

                    </form>
            </div>

        </div>
    @endcan
    <script type="text/javascript">
        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
    </script>
@endsection
