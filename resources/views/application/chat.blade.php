@extends('header_menu')
@section('title')
    Чат
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


    {{-- <div class="container">
        @can('company')
            @if (isset($only_me) && $only_me == true)
                <form action="/chat/{{ $id }}" method="get">
                    <input type="hidden" name="only_me" value=0>
                    <input class="nav-link" type="submit" value="Показать все сообщения">
                </form>
            @else
            @endcan
            <form action="/chat/{{ $id }}" method="get">
                <input type="hidden" name="only_me" value=1>
                <input class="nav-link" type="submit" value="Показать только мои сообщения">
            </form>
        @endif
        <div class="row">

            <div class="col">

                <ul class="list-unstyled">

                    @foreach ($chats as $key => $chat)
                        @if ($chat->from == Auth::user()->id)
                            <li class="d-flex justify-content-between mb-4">
                                <div width=90px></div>
                                <div class="card w-50">
                                    <div class="card-header d-flex justify-content-between p-3">
                                        <p class="fw-bold mb-0">Кому:
                                            @if (isset($chat->to_name))
                                                {{ $chat->to_name }}
                                            @endif
                                            @foreach ($chat->user_to as $user_to)
                                                <br> {{ $user_to->to_name }}
                                            @endforeach


                                        </p>
                                        <p class="text-muted small mb-0"><i class="far fa-clock"></i>
                                            {{ Date::parse($chat->created_at)->format('j F Yг. H:i') }}</p>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">
                                            {{ $chat->message }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @else
                            <li class="d-flex justify-content-between mb-4">
                                <div class="card w-50">
                                    <div class="card-header d-flex justify-content-between p-8">
                                        <p class="fw-bold mb-0"> От: {{ $chat->from_name }} </p>
                                        <p class=" mb-0 text-muted"> Кому:
                                            @if (isset($chat->to_name))
                                                {{ $chat->to_name }}
                                            @endif
                                            @foreach ($chat->user_to as $user_to)
                                                <br> {{ $user_to->to_name }}
                                            @endforeach

                                        </p>
                                        <p class="text-muted small mb-0"><i class="far fa-clock"></i>
                                            {{ Date::parse($chat->created_at)->format('j F Yг. H:i') }}</p>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">
                                            {{ $chat->message }}
                                        </p>
                                        <a href="/chat/{{ $id }}?selected={{ $chat->from }}&mess_id={{ $key }}#answer"
                                            class="msg-but"><img src="/storage/img/reply.png" width="20"
                                                alt="Ответить"></a>

                                    </div>

                                </div>
                        @endif



                        </li>
                    @endforeach
                    <form action="/chat_sent/{{ $id }}" method="post" id='myForm'>
                        @csrf
                        <div class="form-float">

                            Выберите получателя (для выбора нескольких получателей удерживайте ctrl или cmd)
                            <Select name="to[]" class="form-cont" multiple id="answer">
                                <option value="0">Все</option>
                                @foreach ($users as $user)
                                    @if ($user->id != Auth::user()->id)
                                        <option value="{{ $user->id }}"
                                            @if ($user->id == $selected) selected @endif>{{ $user->name }}</option>
                                    @endif
                                @endforeach
                            </Select>
                        </div>
                        <br><br><br><br>
                        <input type="hidden" name="from" value="{{ Auth::user()->id }}">
                        <li class="bg-white mb-3">

                            <div class="form-outline">
                                <textarea class="form-cont" id="textAreaExample2" rows="4" name=message>
                                    @if ($selected)
                                    <<<<<<<{{ $chats[$mess_id]->message }}>>>>>>
                                    @endif
                                </textarea>
                                <label class="form-label" for="textAreaExample2">Message</label>
                            </div>
                        </li>
                        <input type="submit" class="nav-link">
                </ul>
                </form>
            </div>

        </div>

    </div> --}}
@endsection
