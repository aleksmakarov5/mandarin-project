@extends('header_menu')
@section('title')
    Причины отказа
@endsection
@section('main')
    <form action="/answer_fail_save/{{ $id }}" method="post">
        @csrf
        @foreach ($fail_vars as $key => $item)
            <p>
                <input type="checkbox" name="variant[{{ $key }}]" id="var"
                    value="{{ $item->id }}">{{ $item->variant }}
            </p>
        @endforeach
        Комментарий
        <textarea name="comment" id="comment" cols="30" rows="10"></textarea>
        <input class="nav-link" type="submit" value="Сохранить">
    </form>
@endsection
