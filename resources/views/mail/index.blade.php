@extends('layout')
@section('title')
    Почта
@endsection
@section('main_content')
    <h1>Все письма</h1>

    {{-- @foreach ($messages as $message)
        <div class="alert alert-warning">
            <h3>
                {{ $message->subject }}

            </h3>
            <p>

                @php
                    $body = $message->body_html;
                    $body_start = mb_strpos($body, '<body');
                    $body_end = mb_strpos($body, '</body>');
                    if ($body_start) {
                        $body = mb_substr($body, $body_start, $body_end - $body_start);
                    }
                    echo $body;
                @endphp


            </p>
        </div>
       
    @endforeach --}}

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
                                    От кого
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
                                        <li>
                                            <div class="thumb"><i class="mdi mdi-file-pdf"></i></div>
                                            <div class="details">
                                                <p class="file-name">Seminar Reports.pdf</p>
                                                <div class="buttons">
                                                    <p class="file-size">678Kb</p>
                                                    <a href="#" class="view">View</a>
                                                    <a href="#" class="download">Download</a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="thumb"><i class="mdi mdi-file-image"></i></div>
                                            <div class="details">
                                                <p class="file-name">Product Design.jpg</p>
                                                <div class="buttons">
                                                    <p class="file-size">1.96Mb</p>
                                                    <a href="#" class="view">View</a>
                                                    <a href="#" class="download">Download</a>
                                                </div>
                                            </div>
                                        </li>
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
