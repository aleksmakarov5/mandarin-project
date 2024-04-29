@extends('layout')
@section('main_content')
    <a href="/Work/{{ $id }}/doc_load" class="nav-link">Назад</a>
    <h1>Проверьте данные</h1>


    @foreach ($works as $page)
        @foreach ($page as $table)
            <table class="table table-striped-columns table-hover table-group-divider table-responsive">
                <tr>
                    <td>Удалить - Объединить с предыдущей </td>
                </tr>
                @foreach ($table as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td> {{ $cell }} </td>
                        @endforeach
                        <td>Удалить строку</td>
                    </tr>
                @endforeach
            </table>
        @endforeach
    @endforeach
@endsection
