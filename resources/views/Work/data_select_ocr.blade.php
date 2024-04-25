@extends('layout')
@section('main_content')
    <a href="/Work/{{ $id }}/doc_load" class="nav-link">Назад</a>
    <h1>Проверьте данные</h1>
    <form action="/Work/{{ $id }}/data_save" method="post">
        @csrf
        <input type="hidden" name="id_application" value="{{ $id }}">
        @for ($i = 0; $i < count(session('works')[0][0]); $i++)
            <input type="hidden" name="name[{{ $i }}]" value="{{ session('works')[0][0][$i][0] }}">
            <input type="hidden" name="price[{{ $i }}]" value="{{ session('works')[0][0][$i][1] }}">
            <input type="hidden" name="count_hours[{{ $i }}]" value="{{ session('works')[0][0][$i][2] }}">
            <input type="hidden" name="summ[{{ $i }}]" value="{{ session('works')[0][0][$i][3] }}">
        @endfor
        <button class="nav-link" type="submit">Сохранить</button>
    </form>

    {{-- <input type="hidden" name="location" value="{{ $location }}"> --}}
    @foreach (session('works') as $page)
        @foreach ($page as $table)
            <table class="table table-striped-columns table-hover table-group-divider table-responsive">
                <tr>
                    <td>Удалить - Объединить с предыдущей </td>
                </tr>
                @foreach ($table as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td> {{ $cell }} </td>
                        @endforach
                        <td>Удалить строку</td>
                    </tr>
                @endforeach
            </table>
        @endforeach
    @endforeach
@endsection
