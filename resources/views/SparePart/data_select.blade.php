@extends('header_menu')
@section('main')
        <a href="/SparePart/{{ $id }}/doc_load" class="nav-link">Назад</a>
        <h1>Проверьте данные</h1>
        <form action="data_save" method="post">
            @csrf
            <input type="hidden" name="id_application" value="{{ $id }}">
            @if ($id_ic == 1)
                @for ($i = 1, $j = 1; $i < count($SpareParts) - 8; $i += 8, $j++)
                    <input type="hidden" name="name[{{ $j }}]" value="{{ $SpareParts[$i + 2] }}">
                    <input type="hidden" name="price_ic_full[{{ $j }}]" value="{{ $SpareParts[$i + 6] }}">
                    <input type="hidden" name="price[{{ $j }}]" value="{{ $SpareParts[$i + 3] }}">
                    <input type="hidden" name="iznos[{{ $j }}]" value="{{ $SpareParts[$i + 4] }}">
                    <input type="hidden" name="count[{{ $j }}]" value="{{ $SpareParts[$i + 5] }}">
                    <input type="hidden" name="part_number[{{ $j }}]"
                        value="@php echo mb_strpos($SpareParts[$i + 1], 'БН') || mb_strpos($SpareParts[$i + 1], 'бн') || $SpareParts[$i + 1] == 'БН' ? $SpareParts[$i + 1] . " " . $j : $SpareParts[$i + 1] @endphp">
                    <input type="hidden" name="price_ic_half[{{ $j }}]" value="{{ $SpareParts[$i + 7] }}">
                @endfor
            @endif
            @if ($id_ic == 3)
                @for ($i = 0, $j = 1; $i < count($SpareParts) - 7; $i += 7, $j++)
                    <input type="hidden" name="name[{{ $j }}]" value="{{ $SpareParts[$i] }}">
                    <input type="hidden" name="price_ic_full[{{ $j }}]" value="{{ $SpareParts[$i + 3] }}">
                    <input type="hidden" name="price[{{ $j }}]" value="{{ $SpareParts[$i + 1] }}">
                    <input type="hidden" name="iznos[{{ $j }}]" value="{{ $SpareParts[$i + 4] }}">
                    <input type="hidden" name="count[{{ $j }}]" value="{{ $SpareParts[$i + 2] }}">
                    <input type="hidden" name="part_number[{{ $j }}]"
                        value="@php echo mb_strpos($SpareParts[$i + 6], 'БН') || mb_strpos($SpareParts[$i + 1], 'бн') || $SpareParts[$i + 6] == 'БН' ? $SpareParts[$i + 6] . " " . $j : $SpareParts[$i + 6] @endphp">
                    <input type="hidden" name="price_ic_half[{{ $j }}]" value="{{ $SpareParts[$i + 5] }}">
                @endfor
            @endif
            @if ($id_ic == 4)
                @for ($i = 0, $j = 1; $i < count($SpareParts) - 7; $i += 7, $j++)
                    <input type="hidden" name="name[{{ $j }}]" value="{{ $SpareParts[$i + 1] }}">
                    <input type="hidden" name="price_ic_full[{{ $j }}]" value="{{ $SpareParts[$i + 2] }}">
                    <input type="hidden" name="price[{{ $j }}]" value="{{ $SpareParts[$i + 3] }}">
                    <input type="hidden" name="iznos[{{ $j }}]" value="{{ $SpareParts[$i + 4] }}">
                    <input type="hidden" name="count[{{ $j }}]" value="{{ $SpareParts[$i + 5] }}">
                    <input type="hidden" name="part_number[{{ $j }}]" value="{{ $SpareParts[$i] }}">
                    <input type="hidden" name="price_ic_half[{{ $j }}]" value="{{ $SpareParts[$i + 6] }}">
                @endfor
            @endif
            <button class="nav-link" type="submit">Сохранить</button>

        </form>
        <form action="data_load" method="POST">
            @csrf
            <input type="hidden" name="location" value="{{ $location }}">
            <table class="table table-striped-columns table-hover table-group-divider table-responsive">
                <thead>

                    <tr>
                        <th scope="col">№пп</th>
                        <th scope="col">Кат № </th>
                        <th scope="col">Наименование</th>
                        <th scope="col">Цена, руб</th>
                        <th scope="col">Износ %</th>
                        <th scope="col">Кол-во</th>
                        <th scope="col">Сумма, руб</th>
                        <th scope="col">Сумма с износом, руб</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($type == 2)

                        @for ($i = 0; $i < count($SpareParts) - 7; $i += 7)
                            <tr>
                                <td> {{ $i/7 }} <input type="checkbox" name='iskl[{{ $i }}]' value={{ $i }}> </td>
                                <td> {{ $SpareParts[$i + 6] }}<input type="checkbox" name='iskl[{{ $i + 1 }}]' value={{ $i + 1 }}> </td>
                                <td> {{ $SpareParts[$i ] }}<input type="checkbox" name='iskl[{{ $i + 2 }}]' value={{ $i + 2 }}> </td>
                                <td> {{ $SpareParts[$i + 3] }}<input type="checkbox" name='iskl[{{ $i + 3 }}]' value={{ $i + 3 }}> </td>
                                <td> {{ $SpareParts[$i + 4] }} <input type="checkbox" name='iskl[{{ $i + 4 }}]' value={{ $i + 4 }}> </td>
                                <td> {{ $SpareParts[$i + 2] }}<input type="checkbox" name='iskl[{{ $i + 5 }}]' value={{ $i + 5 }}> </td>
                                <td> {{ $SpareParts[$i + 1] }}<input type="checkbox" name='iskl[{{ $i + 6 }}]' value={{ $i + 6 }}> </td>
                                <td> {{ $SpareParts[$i + 5] }}<input type="checkbox" name='iskl[{{ $i + 7 }}]' value={{ $i + 7 }}> </td>

                            </tr>
                        @endfor
                    @else


                    @if ($id_ic == 1)
                        @for ($i = 1; $i < count($SpareParts) - 8; $i += 8)
                            <tr>
                                <td> {{ $SpareParts[$i] }} <input type="checkbox" name='iskl[{{ $i }}]'
                                        value={{ $i }}> </td>
                                <td> {{ $SpareParts[$i + 1] }}<input type="checkbox"
                                        name='iskl[{{ $i + 1 }}]'value={{ $i + 1 }}> </td>
                                <td> {{ $SpareParts[$i + 2] }}<input type="checkbox"
                                        name='iskl[{{ $i + 2 }}]'value={{ $i + 2 }}> </td>
                                <td> {{ $SpareParts[$i + 3] }}<input type="checkbox"
                                        name='iskl[{{ $i + 3 }}]'value={{ $i + 3 }}> </td>
                                <td> {{ $SpareParts[$i + 4] }} <input type="checkbox" name='iskl[{{ $i + 4 }}]'
                                        value={{ $i + 4 }}> </td>
                                <td> {{ $SpareParts[$i + 5] }}<input type="checkbox"
                                        name='iskl[{{ $i + 5 }}]'value={{ $i + 5 }}> </td>
                                <td> {{ $SpareParts[$i + 6] }}<input type="checkbox"
                                        name='iskl[{{ $i + 6 }}]'value={{ $i + 6 }}> </td>
                                <td> {{ $SpareParts[$i + 7] }}<input type="checkbox"
                                        name='iskl[{{ $i + 7 }}]'value={{ $i + 7 }}> </td>

                            </tr>
                        @endfor
                    @endif
                    @if ($id_ic == 3)
                        @for ($i = 0, $j = 1; $i < count($SpareParts) - 7; $i += 7, $j++)
                            <tr>
                                <td> {{ $j }} </td>
                                <td> @php echo mb_strpos($SpareParts[$i + 6], 'БН') || mb_strpos($SpareParts[$i + 6], 'бн') || $SpareParts[$i + 6] == 'БН' ? $SpareParts[$i + 6] . " " . $j : $SpareParts[$i + 6] @endphp <input type="checkbox" name='iskl[{{ $i + 6 }}]'
                                        value={{ $i + 6 }}> </td>
                                <td> {{ $SpareParts[$i] }}<input type="checkbox"
                                        name='iskl[{{ $i }}]'value={{ $i }}> </td>
                                <td> {{ $SpareParts[$i + 1] }}<input type="checkbox"
                                        name='iskl[{{ $i + 1 }}]'value={{ $i + 1 }}> </td>
                                <td> {{ $SpareParts[$i + 4] }}<input type="checkbox"
                                        name='iskl[{{ $i + 4 }}]'value={{ $i + 4 }}> </td>
                                <td> {{ $SpareParts[$i + 2] }} <input type="checkbox" name='iskl[{{ $i + 2 }}]'
                                        value={{ $i + 2 }}> </td>
                                <td> {{ $SpareParts[$i + 3] }}<input type="checkbox"
                                        name='iskl[{{ $i + 3 }}]'value={{ $i + 3 }}> </td>
                                <td> {{ $SpareParts[$i + 5] }}<input type="checkbox"
                                        name='iskl[{{ $i + 5 }}]'value={{ $i + 5 }}> </td>
                            </tr>
                        @endfor
                    @endif
                    @if ($id_ic == 4)
                        @for ($i = 0, $j = 1; $i < count($SpareParts) - 7; $i += 7, $j++)
                            <tr>
                                <td> {{ $j }} </td>
                                <td> {{ $SpareParts[$i] }}<input type="checkbox"
                                        name='iskl[{{ $i }}]'value={{ $i }}> </td>
                                <td> {{ $SpareParts[$i + 1] }}<input type="checkbox"
                                        name='iskl[{{ $i + 1 }}]'value={{ $i + 1 }}> </td>
                                <td> {{ $SpareParts[$i + 2] }}<input type="checkbox"
                                        name='iskl[{{ $i + 2 }}]'value={{ $i + 2 }}> </td>
                                <td> {{ $SpareParts[$i + 3] }}<input type="checkbox"
                                        name='iskl[{{ $i + 3 }}]'value={{ $i + 3 }}> </td>
                                <td> {{ $SpareParts[$i + 4] }} <input type="checkbox" name='iskl[{{ $i + 4 }}]'
                                        value={{ $i + 4 }}> </td>
                                <td> {{ $SpareParts[$i + 5] }}<input type="checkbox"
                                        name='iskl[{{ $i + 5 }}]'value={{ $i + 5 }}> </td>
                                <td> {{ $SpareParts[$i + 6] }}<input type="checkbox"
                                        name='iskl[{{ $i + 6 }}]'value={{ $i + 6 }}> </td>
                            </tr>
                        @endfor
                    @endif
    @endif

                </tbody>
            </table>
            @if ($reset > 0)
                <input type="hidden" name="reset" value=1>
                <button class="nav-link" type="submit">Сброс</button>
            @else
                <button class="nav-link" type="submit">Удалить выбранные ячейки</button>
            @endif
        </form>
@endsection
