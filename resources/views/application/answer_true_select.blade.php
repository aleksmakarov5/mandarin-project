@extends('header_menu')
@section('title')
    Данные для ответа
@endsection
@section('main')
    <form action="/answer_true_save/{{ $id }}" method="post">
        @csrf
        @foreach ($true_vars as $key => $item)
            <p>
                <input type="checkbox" name="variant[{{ $key }}]" id="var"
                    value="{{ $item->id }}">{{ $item->variant }}
            </p>
        @endforeach
        Комментарий <br>
        <textarea name="comment" id="comment" cols="90" rows="10"></textarea>
        <input class="nav-link" type="submit" value="Сохранить">
    </form>
@endsection
