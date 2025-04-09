@extends('header_menu')
@section('title')
    Переписка с СК
@endsection
@section('main')


@can('admin')

        @php
            $to=[];
           if (isset($application->id_insuranceCompany)){
                if ($application->id_insuranceCompany == 3)
                    {$to[0]= isset($to_email)?$to_email:'';
           }
                    elseif ($application->id_insuranceCompany == 1) {
                        $to[0]= "service_offers@vsk.ru";
                    }
                    else {
                       $to[0]= isset($to_email)?$to_email:'';
                    }
                    }
                    else {
                       $to[0]= isset($to_email)?$to_email:'';
                    }

        @endphp
 <table class="table">
        <tr>

                <td>
                    <h1>Акцепт</h1>

                </td>
                <td>
    <div>
<answer-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
    :title="{{json_encode('Акцепт на условиях СК')}}"
                :subject="{{json_encode(isset($subject)?$subject:'---')}}"
                :attach="{{json_encode(isset($attach)?$attach:[])}}" :app_id="{{$id}}"
:text="{{json_encode('Добрый день.<br>
Ремонт автомобиля по заданным условиям акцептован.<br>
Комментарий (при необходимости) :')}}"
                :sign="{{json_encode(isset($sign)?$sign:'')}}"
                :status="22" />
                </div>
                </td>
                <td>
            <div>
                <answer-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
    :title="{{json_encode('Акцепт (иная стоимость ремонта)')}}"
                :subject="{{json_encode(isset($subject)?$subject:'---')}}"
                :attach="{{json_encode(isset($attach)?$attach:[])}}" :app_id="{{$id}}"
:text="{{json_encode('Добрый день.<br>
Ремонт автомобиля акцептован, но на иных условиях (иная сумма ремонта).<br>
ВАЖНО!Ответ должен содержать точную сумму ремонта или условия ЕМР, с учетом изменений по скидке/наценке в %).<br>
Комментарий (обязательно):')}}"
                :sign="{{json_encode(isset($sign)?$sign:'')}}"
                :status="22" />
                </div>
                </td>
                <td>
            <div>
                <answer-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
    :title="{{json_encode('Акцепт (иная скидка от ЕМР)')}}"
                :subject="{{json_encode(isset($subject)?$subject:'---')}}"
                :attach="{{json_encode(isset($attach)?$attach:[])}}" :app_id="{{$id}}"
:text="{{json_encode('Добрый день.<br>
Ремонт автомобиля акцептован, но на иных условиях (иная скидка от ЕМР).<br>
ВАЖНО!Ответ должен содержать точную сумму ремонта или условия ЕМР, с учетом изменений по скидке/наценке в %).<br>
Комментарий (обязательно):')}}"
                :sign="{{json_encode(isset($sign)?$sign:'')}}"
                :status="22" />
                </div>
                </td>
                <td>
            <div>
                <answer-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
    :title="{{json_encode('Акцепт (б/у запчасти)')}}"
                :subject="{{json_encode(isset($subject)?$subject:'---')}}"
                :attach="{{json_encode(isset($attach)?$attach:[])}}" :app_id="{{$id}}"
:text="{{json_encode('Добрый день.<br>
Ремонт автомобиля акцептован, но на иных условиях (только б/у запчасти).<br>
Комментарий (обязательно):')}}"
                :sign="{{json_encode(isset($sign)?$sign:'')}}"
                :status="22" />
                </div>
                </td>
                <td>
            <div>
                <answer-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
    :title="{{json_encode('Акцепт (иные сроки ремонта)')}}"
                :subject="{{json_encode(isset($subject)?$subject:'---')}}"
                :attach="{{json_encode(isset($attach)?$attach:[])}}" :app_id="{{$id}}"
:text="{{json_encode('Добрый день.<br>
Ремонт автомобиля акцептован, но нет невозможности уложиться в срок 30 р/д.<br>
Комментарий (обязательно укажите в какой срок готовы осуществить ремонт):')}}"
                :sign="{{json_encode(isset($sign)?$sign:'')}}"
                :status="22" />
                </div>
                </td>
        </tr>
 </table>
  <table class="table">
        <tr>

                <td>
                    <h1>Отказ</h1>

                </td>

                <td>
            <div>
                <answer-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
    :title="{{json_encode('Отсутствие запасных частей')}}"
                :subject="{{json_encode(isset($subject)?$subject:'---')}}"
                :attach="{{json_encode(isset($attach)?$attach:[])}}" :app_id="{{$id}}"
:text="{{json_encode('Добрый день.<br>
От ремонта отказываемся ввиду отсутствия запасных частей.<br>
Комментарий (обязательно укажите, какие запасные части отсутствуют):')}}"
                :sign="{{json_encode(isset($sign)?$sign:'')}}"
                :status="22" />
                </div>
                </td>
                <td>
            <div>
                <answer-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
    :title="{{json_encode('Ремонт невозможен без доп работ (не относящихся к событию)')}}"
                :subject="{{json_encode(isset($subject)?$subject:'---')}}"
                :attach="{{json_encode(isset($attach)?$attach:[])}}" :app_id="{{$id}}"
:text="{{json_encode('Добрый день.<br>
От ремонта отказываемся ввиду невозможности ремонта автомобиля по страховому случаю без проведения дополнительных работ, не относящихся к событию.')}}"
                :sign="{{json_encode(isset($sign)?$sign:'')}}"
                :status="22" />
                </div>
                </td>
                  <td>
            <div>
                <answer-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
    :title="{{json_encode('Ремонт невозможен: наличие сквозной коррозии')}}"
                :subject="{{json_encode(isset($subject)?$subject:'---')}}"
                :attach="{{json_encode(isset($attach)?$attach:[])}}" :app_id="{{$id}}"
:text="{{json_encode('Добрый день.<br>
От ремонта отказываемся ввиду наличия сквозной коррозии.')}}"
                :sign="{{json_encode(isset($sign)?$sign:'')}}"
                :status="22" />
                </div>
                </td>
        </tr>
    </table>
@endcan


    <div class="content-wrapper">
        <div class="email-wrapper wrapper">

            <div class="row align-items-stretch">
                @foreach ($messages as $message)
                    <div class="sender-details">
                        <div class="details">
                            <p class="msg-subject">
                            <h1>{{ $message->subject }}</h1>
                            </p>
                            <h3>
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
                            </h3>
                        </div>


                        <h3>
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
                        </h3>
                    </div>
                    <div class="mail-view">
                        <div class="row">
                            <div class="col">
                                <div class="btn-toolbar">
                                    {{-- <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i
                                                    class="mdi mdi-reply text-primary mr-1"></i> Ответить</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i
                                                    class="mdi mdi-reply-all text-primary mr-1"></i>Ответить всем</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i
                                                    class="mdi mdi-share text-primary mr-1"></i>Переслать</button>
                                        </div> --}}
                                    <div class="btn-group">
                                        <a class="msg_but" href="/mail_delete/{{ $message->id }}" title="Удалить"><img
                                                src="/storage/img/delete.png" width="20" alt="Удалить"></a>
                                        <a href="/applications/move_mail_index/{{ $message->application_id }}/{{ $message->mailbox_id }}/{{ $message->id }}"
                                            class="msg_but" title="Переместить в другую заявку"><img
                                                src="/storage/img/move.png" width="20" alt="Переместить"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="message-body">
                            <div class="message-content">
                                @php
    echo $message->body;
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
                @endforeach
            </div>
        </div>
    </div>
    @can('admin')

        @php
            $to=[];
            $to[0]= $to_email;
        @endphp
            <new-mail :to="{{json_encode($to)}}" :from="{{ Auth::user()->id }}"
                :subject="{{json_encode(isset($subject)?$subject:'---')}}"
                :attach="{{json_encode(isset($attach)?$attach:[])}}" :app_id="{{$id}}"
                :sign="{{json_encode(isset($sign)?$sign:'')}}"
                :status="22" />
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
                                        @if (isset($attach))
                                            @foreach ($attach as $key => $doc)
                                                <div class="card" style="width: 20rem;">
                                                    <div class="row g-0">
                                                        <div class="col-md-4">
                                                            <form name="form{{ $key }}" action="data_load" method="POST">
                                                                @csrf
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
                                        @endif
                                    </div>
                                    <input type="submit" class="nav-link">

                            </form>
                    </div>

                </div>
    @endcan

@endsection
