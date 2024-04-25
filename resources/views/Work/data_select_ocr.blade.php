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
    <form action="data_load" method="POST">
        @csrf
        {{-- <input type="hidden" name="location" value="{{ $location }}"> --}}
        @foreach ($file as $page)
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
        {{-- <table class="table table-striped-columns table-hover table-group-divider table-responsive">
            <thead>

                <tr>
                    <th scope="col">Наименование</th>
                    <th scope="col">Цена ед., руб. </th>
                    <th scope="col">Количество нормочасов</th>
                    <th scope="col">Сумма,руб</th>
                </tr>
            </thead>
            <tbody>

                @for ($i = 0; $i < count(session('works')) - 3; $i += 4)
                    <tr>
                        <td> {{ session('works')[$i] }}
                            @if (!is_numeric(str_replace(' ', '', session('works')[$i])))
                                <br>
                                <a href="/work_edit/{{ $id }}/{{ $i }}/1">
                                    <img src="/storage/img/left.png" alt="Влево" width=25px
                                        title="Объеденить с ячейкой слева">
                                </a>
                                <a href="/work_edit/{{ $id }}/{{ $i }}/4"><img
                                        src="/storage/img/up.png" alt="Вверх" width=25px
                                        title="Объеденить с ячейкой сверху"></a>
                            @endif
                        </td>
                        <td> {{ session('works')[$i + 1] }}
                            @if (!is_numeric(str_replace(' ', '', session('works')[$i + 1])))
                                <br>
                                <a href="/work_edit/{{ $id }}/{{ $i + 1 }}/1"><img
                                        src="/storage/img/left.png" alt="Влево" width=25px
                                        title="Объеденить с ячейкой слева"></a>
                                <a href="/work_edit/{{ $id }}/{{ $i + 1 }}/4"><a
                                        href="/work_edit/{{ $id }}/{{ $i }}/4"><img
                                            src="/storage/img/up.png" alt="Вверх" width=25px
                                            title="Объеденить с ячейкой сверху"></a></a>
                            @endif
                        </td>
                        <td> {{ session('works')[$i + 2] }}
                            @if (!is_numeric(str_replace(' ', '', session('works')[$i + 2])))
                                <br>
                                <a href="/work_edit/{{ $id }}/{{ $i + 2 }}/1"><img
                                        src="/storage/img/left.png" alt="Влево" width=25px
                                        title="Объеденить с ячейкой слева"></a>
                                <a href="/work_edit/{{ $id }}/{{ $i + 2 }}/4"><a
                                        href="/work_edit/{{ $id }}/{{ $i }}/4"><img
                                            src="/storage/img/up.png" alt="Вверх" width=25px
                                            title="Объеденить с ячейкой сверху"></a></a>
                            @endif
                        </td>
                        <td> {{ session('works')[$i + 3] }}
                            @if (!is_numeric(str_replace(' ', '', session('works')[$i + 3])))
                                <a href="/work_edit/{{ $id }}/{{ $i + 3 }}/0">Удалить</a>
                                <a href="/work_edit/{{ $id }}/{{ $i + 3 }}/1"><img
                                        src="/storage/img/left.png" alt="Влево" width=25px
                                        title="Объеденить с ячейкой слева"></a>
                                <a href="/work_edit/{{ $id }}/{{ $i + 3 }}/4"><a
                                        href="/work_edit/{{ $id }}/{{ $i }}/4"><img
                                            src="/storage/img/up.png" alt="Вверх" width=25px
                                            title="Объеденить с ячейкой сверху"></a></a>
                            @endif
                        </td>

                    </tr>
                @endfor --}}
        {{-- </tbody>
        </table> --}}
        {{-- @if ($reset > 0)
            <input type="hidden" name="reset" value=1>
            <button class="nav-link" type="submit">Сброс</button>
        @else
            <button class="nav-link" type="submit">Удалить выбранные ячейки</button>
        @endif --}}
    </form>
@endsection
