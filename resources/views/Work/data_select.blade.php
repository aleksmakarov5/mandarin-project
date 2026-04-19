@extends('layout')
@section('main_content')
    <a href="/Work/{{ $id }}/doc_load" class="nav-link">Назад</a>
    <h1>Проверьте данные</h1>
    <form action="/Work/{{ $id }}/data_save" method="post">
        @csrf
        <input type="hidden" name="id_application" value="{{ $id }}">
        @if (isset($id_ic) && $id_ic == 4)
            @for ($i = 0, $j = 1; $i < count(session('works')) - 4; $i += 5, $j++)
                <input type="hidden" name="name[{{ $j }}]" value="{{ session('works')[$i] }}">
                <input type="hidden" name="price[{{ $j }}]" value="{{ session('works')[$i + 1] }}">
                <input type="hidden" name="count_hours[{{ $j }}]" value="{{ session('works')[$i + 2] }}">
                <input type="hidden" name="summ[{{ $j }}]" value="{{ session('works')[$i + 4] }}">
            @endfor
            @if (session('works_paint'))
                @for ($i = 0, $j = 1; $i < count(session('works_paint')) - 4; $i += 5, $j++)
                    <input type="hidden" name="name_paint[{{ $j }}]" value="{{ session('works_paint')[$i] }}">
                    <input type="hidden" name="price_paint[{{ $j }}]"
                        value="{{ session('works_paint')[$i + 1] }}">
                    <input type="hidden" name="count_hours_paint[{{ $j }}]"
                        value="{{ session('works_paint')[$i + 2] }}">
                    <input type="hidden" name="summ_paint[{{ $j }}]"
                        value="{{ session('works_paint')[$i + 4] }}">
                @endfor
            @endif
        @else
            @for ($i = 0, $j = 1; $i < count(session('works')) - 3; $i += 4, $j++)
                <input type="hidden" name="name[{{ $j }}]" value="{{ session('works')[$i] }}">
                <input type="hidden" name="price[{{ $j }}]" value="{{ session('works')[$i + 1] }}">
                <input type="hidden" name="count_hours[{{ $j }}]" value="{{ session('works')[$i + 2] }}">
                <input type="hidden" name="summ[{{ $j }}]" value="{{ session('works')[$i + 3] }}">
            @endfor
            @if (session('works_paint'))
                @for ($i = 0, $j = 1; $i < count(session('works_paint')) - 3; $i += 4, $j++)
                    <input type="hidden" name="name_paint[{{ $j }}]" value="{{ session('works_paint')[$i] }}">
                    <input type="hidden" name="price_paint[{{ $j }}]"
                        value="{{ session('works_paint')[$i + 1] }}">
                    <input type="hidden" name="count_hours_paint[{{ $j }}]"
                        value="{{ session('works_paint')[$i + 2] }}">
                    <input type="hidden" name="summ_paint[{{ $j }}]"
                        value="{{ session('works_paint')[$i + 3] }}">
                @endfor
            @endif
        @endif

        <button class="nav-link" type="submit">Сохранить</button>
    </form>
    <form action="data_load" method="POST">
        @csrf
        {{-- <input type="hidden" name="location" value="{{ $location }}"> --}}
        <h3>Ремонтные работы</h3>
        <table class="table table-striped-columns table-hover table-group-divider table-responsive">
            <thead>

                <tr>
                    <th scope="col">Наименование</th>
                    <th scope="col">Цена ед., руб. </th>
                    <th scope="col">Количество нормочасов</th>
                    <th scope="col">Сумма,руб</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($id_ic) && $id_ic == 4)
                    @for ($i = 0; $i < count(session('works')) - 4; $i += 5)
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
                            <td> {{ session('works')[$i + 4] }}
                                @if (!is_numeric(str_replace(' ', '', session('works')[$i + 4])))
                                    <a href="/work_edit/{{ $id }}/{{ $i + 4 }}/0">Удалить</a>
                                    <a href="/work_edit/{{ $id }}/{{ $i + 4 }}/1"><img
                                            src="/storage/img/left.png" alt="Влево" width=25px
                                            title="Объеденить с ячейкой слева"></a>
                                    <a href="/work_edit/{{ $id }}/{{ $i + 4 }}/4"><a
                                            href="/work_edit/{{ $id }}/{{ $i }}/4"><img
                                                src="/storage/img/up.png" alt="Вверх" width=25px
                                                title="Объеденить с ячейкой сверху"></a></a>
                                @endif
                            </td>

                        </tr>
                    @endfor
                @else
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
                    @endfor
                @endif

            </tbody>
        </table>
        @if (session('works_paint'))
            <h3>Окрасочные работы</h3>
            <table class="table table-striped-columns table-hover table-group-divider table-responsive">
                <thead>

                    <tr>
                        <th scope="col">Наименование</th>
                        <th scope="col">Цена ед., руб. </th>
                        <th scope="col">Количество нормочасов</th>
                        <th scope="col">Сумма,руб</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($id_ic) && $id_ic == 4)
                        @for ($i = 0; $i < count(session('works_paint')) - 4; $i += 5)
                            <tr>
                                <td> {{ session('works_paint')[$i] }}
                                    @if (!is_numeric(str_replace(' ', '', session('works_paint')[$i])))
                                        <br>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i }}/1">
                                            <img src="/storage/img/left.png" alt="Влево" width=25px
                                                title="Объеденить с ячейкой слева">
                                        </a>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i }}/4"><img
                                                src="/storage/img/up.png" alt="Вверх" width=25px
                                                title="Объеденить с ячейкой сверху"></a>
                                    @endif
                                </td>
                                <td> {{ session('works_paint')[$i + 1] }}
                                    @if (!is_numeric(str_replace(' ', '', session('works_paint')[$i + 1])))
                                        <br>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 1 }}/1"><img
                                                src="/storage/img/left.png" alt="Влево" width=25px
                                                title="Объеденить с ячейкой слева"></a>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 1 }}/4"><a
                                                href="/work_edit_paint/{{ $id }}/{{ $i }}/4"><img
                                                    src="/storage/img/up.png" alt="Вверх" width=25px
                                                    title="Объеденить с ячейкой сверху"></a></a>
                                    @endif
                                </td>
                                <td> {{ session('works_paint')[$i + 2] }}
                                    @if (!is_numeric(str_replace(' ', '', session('works_paint')[$i + 2])))
                                        <br>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 2 }}/1"><img
                                                src="/storage/img/left.png" alt="Влево" width=25px
                                                title="Объеденить с ячейкой слева"></a>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 2 }}/4"><a
                                                href="/work_edit_paint/{{ $id }}/{{ $i }}/4"><img
                                                    src="/storage/img/up.png" alt="Вверх" width=25px
                                                    title="Объеденить с ячейкой сверху"></a></a>
                                    @endif
                                </td>
                                <td> {{ session('works_paint')[$i + 4] }}
                                    @if (!is_numeric(str_replace(' ', '', session('works')[$i + 4])))
                                        <a
                                            href="/work_edit_paint/{{ $id }}/{{ $i + 4 }}/0">Удалить</a>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 4 }}/1"><img
                                                src="/storage/img/left.png" alt="Влево" width=25px
                                                title="Объеденить с ячейкой слева"></a>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 4 }}/4"><a
                                                href="/work_edit_paint/{{ $id }}/{{ $i }}/4"><img
                                                    src="/storage/img/up.png" alt="Вверх" width=25px
                                                    title="Объеденить с ячейкой сверху"></a></a>
                                    @endif
                                </td>

                            </tr>
                        @endfor
                    @else
                        @for ($i = 0; $i < count(session('works_paint')) - 3; $i += 4)
                            <tr>
                                <td> {{ session('works_paint')[$i] }}
                                    @if (!is_numeric(str_replace(' ', '', session('works_paint')[$i])))
                                        <br>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i }}/1">
                                            <img src="/storage/img/left.png" alt="Влево" width=25px
                                                title="Объеденить с ячейкой слева">
                                        </a>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i }}/4"><img
                                                src="/storage/img/up.png" alt="Вверх" width=25px
                                                title="Объеденить с ячейкой сверху"></a>
                                    @endif
                                </td>
                                <td> {{ session('works_paint')[$i + 1] }}
                                    @if (!is_numeric(str_replace(' ', '', session('works_paint')[$i + 1])))
                                        <br>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 1 }}/1"><img
                                                src="/storage/img/left.png" alt="Влево" width=25px
                                                title="Объеденить с ячейкой слева"></a>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 1 }}/4"><a
                                                href="/work_edit_paint/{{ $id }}/{{ $i }}/4"><img
                                                    src="/storage/img/up.png" alt="Вверх" width=25px
                                                    title="Объеденить с ячейкой сверху"></a></a>
                                    @endif
                                </td>
                                <td> {{ session('works_paint')[$i + 2] }}
                                    @if (!is_numeric(str_replace(' ', '', session('works_paint')[$i + 2])))
                                        <br>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 2 }}/1"><img
                                                src="/storage/img/left.png" alt="Влево" width=25px
                                                title="Объеденить с ячейкой слева"></a>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 2 }}/4"><a
                                                href="/work_edit_paint/{{ $id }}/{{ $i }}/4"><img
                                                    src="/storage/img/up.png" alt="Вверх" width=25px
                                                    title="Объеденить с ячейкой сверху"></a></a>
                                    @endif
                                </td>
                                <td> {{ session('works_paint')[$i + 3] }}
                                    @if (!is_numeric(str_replace(' ', '', session('works_paint')[$i + 3])))
                                        <a
                                            href="/work_edit_paint/{{ $id }}/{{ $i + 3 }}/0">Удалить</a>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 3 }}/1"><img
                                                src="/storage/img/left.png" alt="Влево" width=25px
                                                title="Объеденить с ячейкой слева"></a>
                                        <a href="/work_edit_paint/{{ $id }}/{{ $i + 3 }}/4"><a
                                                href="/work_edit_paint/{{ $id }}/{{ $i }}/4"><img
                                                    src="/storage/img/up.png" alt="Вверх" width=25px
                                                    title="Объеденить с ячейкой сверху"></a></a>
                                    @endif
                                </td>

                            </tr>
                        @endfor
                    @endif

                </tbody>
            </table>
        @endif
        {{-- @if ($reset > 0)
        <input type="hidden" name="reset" value=1>
        <button class="nav-link" type="submit">Сброс</button>
        @else
        <button class="nav-link" type="submit">Удалить выбранные ячейки</button>
        @endif --}}
    </form>
@endsection
