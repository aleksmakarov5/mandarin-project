@extends('header_menu')
@section('title')
    Ответ в страховую
@endsection
@section('main')
    {{--
<div class="alert" role="alert" style="width: 100%; background: rgb(116, 116, 255);">
   <div class="card-body">
    <h5 class="card-title"> 
   От: {{ $chat->from}}
</h5>
<h5 class="card-title">
    Кому: {{ $chat->to}}
</h5>
<h6 class="card-subtitle mb-2 text-muted">
    {{ $chat->created_at }}
</h6>
<p class="card-text">
    {{ $chat->message }}
</p>
</div>
</div>



<form action="/chat_send/{{$id}}" method="post">
    @csrf

</form> --}}


    <div class="container">

        <div class="row">

            <div class="col">

                <ul class="list-unstyled">


                    <form action="/answer_ic_true_sent/{{ $id }}" method="post">
                        @csrf

                        <input type="hidden" name="subject"
                            value="@if (isset($subject)) {{ $subject }} @else ---- @endif">
                        <div class="form-float">
                            <input type="text" name="to" id="to" class="form-cont"
                                value="{{ $to }}">
                            <label for="to">Получатель</label>
                        </div>
                        <input type="hidden" name="from" value="{{ Auth::user()->id }}">
                        <li class="bg-white mb-3">
                            <div class="alert  alert-success">
                                {{ $application->comment_true }}
                            </div>
                            <div class="form-outline">
                                <textarea class="form-cont" id="textAreaExample2" rows="15" name=message>
Добрый день! 


По заявке {{ $application->numberInsuranceCompanyApplication }} принято положительное решение @if (isset($true_vars[0]))
со следующими условиями:

@foreach ($true_vars as $item)
-{{ $item->variant }}
@endforeach
@endif


Автомобиль будет поставлен на сервис "ИП "Ананьин Михаил Юрьевич" по адресу {{ $service->service_description }}


Просим прислать направление и калькуляцию
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
                </ul>
                </form>
            </div>

        </div>

    </div>
@endsection
