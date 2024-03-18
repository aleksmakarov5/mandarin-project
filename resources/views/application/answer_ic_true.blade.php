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
                        <div class="form-floating">
                            <input type="text" name="to" id="to" class="form-control"
                                value="{{ $to }}">
                            <label for="to">Получатель</label>
                        </div>
                        <input type="hidden" name="from" value="{{ Auth::user()->id }}">
                        <li class="bg-white mb-3">
                            <div class="alert  alert-success">
                                {{ $application->comment_true }}
                            </div>
                            <div class="form-outline">
                                <textarea class="form-control" id="textAreaExample2" rows="15" name=message>
Добрый день! 
По заявке {{ $application->numberInsuranceCompanyApplication }} принято положительное решение @if (isset($true_vars[0]))
со следующими условиями:

@foreach ($true_vars as $item)
-{{ $item->variant }}
@endforeach
@endif

Просим прислать направление и калькуляцию
              </textarea>

                            </div>
                        </li>
                        <input type="submit" class="nav-link">
                </ul>
                </form>
            </div>

        </div>

    </div>
@endsection
