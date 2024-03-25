@extends('header_menu')
@section('title')
    Запчасти
@endsection
@section('main')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <a href="/SparePart/{{ $id }}/doc_load" class="nav-link">Загрузить данные из калькуляции</a>

    <table @if (Auth::user()->GroupId == 6) id="demo" @endif class="editable">
        <thead>

            <tr>
                <th scope="col">#</th>
                <th scope="col">Наименование</th>
                <th scope="col">Кат. номер</th>
                <th scope="col">Заказанный номер</th>
                <th scope="col">Цена СК</th>
                <th scope="col">Цена СК с износом</th>
                <th scope="col" colspan="2">Цена оригинал</th>
                <th scope="col" colspan="2">Цена аналог</th>
                <th scope="col" colspan="2">Цена б/у</th>
                <th scope="col">Ремонт</th>
                <th scope="col">Разница от полной цены</th>
                <th scope="col">Разница от цены с учетом износа</th>
                <th scope="col">Комментарий</th>
                @if (session('rout') == 'directions')
                    <th scope="col">Статус</th>
                    <th scope="col">Дата заказа</th>
                    <th scope="col">Ожидаемая дата получения</th>
                @endif



            </tr>
            <tr>
                <th scope="col"></th>
                <th scope="col">Итого</th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col">{{ $summ_ic_full }}</th>
                <th scope="col">{{ $summ_ic_half }}</th>
                <th scope="col" colspan="7"></th>
                <th scope="col">
                    <div
                        class="
       @if ($delta_res_full < 0) alert alert-success
       @else
       alert alert-danger @endif
       ">
                        {{ $delta_res_full }}
                    </div>
                </th>
                <th scope="col">
                    <div
                        class="
       @if ($delta_res_half < 0) alert alert-success
       @else
       alert alert-danger @endif
       ">
                        {{ $delta_res_half }}
                    </div>
                </th>
                <th scope="col"></th>
                @if (session('rout') == 'directions')
                    <th scope="col"></th>
                    <th scope="col"></th>


                    <th scope="col"></th>
                @endif
            </tr>
            <tr>
                <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text0"
                        onkeyup="tableSearch(0,'demo')">
                </th>
                <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text1"
                        onkeyup="tableSearch(1,'demo')"></th>
                <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text2"
                        onkeyup="tableSearch(2,'demo')"></th>
                <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text3"
                        onkeyup="tableSearch(3,'demo')"></th>
                <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text4"
                        onkeyup="tableSearch(4,'demo')"></th>
                <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text5"
                        onkeyup="tableSearch(5,'demo')"> </th>
                <th scope="col" colspan="2"><input class="form-control" type="text" placeholder=""
                        id="search-text6" onkeyup="tableSearch(6,'demo')"></th>
                <th scope="col" colspan="2"><input class="form-control" type="text" placeholder=""
                        id="search-text8" onkeyup="tableSearch(8,'demo')"></th>
                <th scope="col" colspan="2"><input class="form-control" type="text" placeholder=" "
                        id="search-text10" onkeyup="tableSearch(10,'demo')"></th>
                <th scope="col"><input class="form-control" type="text" placeholder="" id="search-text11"
                        onkeyup="tableSearch(11),'demo')"></th>
                <th scope="col" colspan=5></th>




            </tr>
        </thead>
        <tbody>



            @foreach ($spareparts as $key => $sparepart)
                <tr @if ($sparepart->author == 5) class="pink" @endif>
                    <form name="form{{ $key }}" id='form{{ $key }}'
                        action="/SparePart/{{ $id }}/update" method="POST">

                        @csrf
                        <input type="hidden" name="id_application" value='{{ $id }}'>


                        <td scope="col" id="td_id-{{ $key }}">{{ $key + 1 }}
                            <a name="{{ $sparepart->id }}"></a>
                        </td>
                        <input type="hidden" name="id" id='id-{{ $key }}' value='{{ $sparepart->id }}'>
                        <td id="td_name-{{ $key }}" scope="col">
                            @if (old('name.' . $key) !== null)
                                {{ old('name.' . $key) }}
                            @else
                                {{ $sparepart->name }}
                            @endif
                        </td>
                        <input type="hidden" name="name" id='name-{{ $key }}'
                            value='{{ $sparepart->name }}'>
                        <td id="td_part_number-{{ $key }}" scope="col">
                            @if (old('part_number.' . $key) !== null)
                                {{ old('part_number.' . $key) }}
                            @else
                                {{ $sparepart->part_number }}
                            @endif
                        </td>
                        <input type="hidden" name="part_number" id='part_number-{{ $key }}'
                            value='{{ $sparepart->part_number }}'>
                        <td id="td_order_number{{ $key }}" scope="col">
                            @if (old('order_number.' . $key) !== null)
                                {{ old('order_number.' . $key) }}
                            @else
                                {{ $sparepart->order_number }}
                            @endif
                        </td>
                        <input type="hidden" name="order_number" id='order_number{{ $key }}'
                            value='{{ $sparepart->order_number }}'>
                        <td id="td_price_ic_full-{{ $key }}" scope="col" class="ic_price">
                            @if (old('price_ic_full.' . $key) !== null)
                                {{ old('price_ic_full.' . $key) }}
                            @else
                                {{ $sparepart->price_ic_full }}
                            @endif
                        </td>
                        <input type="hidden" name="price_ic_full" id='price_ic_full-{{ $key }}'
                            value='{{ $sparepart->price_ic_full }}'>
                        <td id="td_price_ic_half-{{ $key }}" scope="col" class="ic_price">
                            @if (old('price_ic_half.' . $key) !== null)
                                {{ old('price_ic_half.' . $key) }}
                            @else
                                {{ $sparepart->price_ic_half }}
                            @endif
                        </td>
                        <input type="hidden" name="price_ic_half" id='price_ic_half-{{ $key }}'
                            value='{{ $sparepart->price_ic_half }}'>
                        <td id="td_price_original-{{ $key }}" scope="col"
                            class="min
      @if ($sparepart->selected_mrk == 1 && $sparepart->selected_price == 1) zapmrk @endif
      @if ($sparepart->selected_mrk == 1) mrk @endif
      @if ($sparepart->selected_price == 1) zap @endif
      "
                            @if (Auth::user()->GroupId != 6 && Auth::user()->GroupId != 5) colspan=2 @endif>
                            @if (old('price_original.' . $key) !== null)
                                {{ old('price_original.' . $key) }}
                            @else
                                {{ $sparepart->price_original }}
                            @endif
                        </td>
                        @if (Auth::user()->GroupId == 6)
                            <th class="min">
                                <div class="form_radio_btn zap">
                                    <input id="radio-1{{ $key }}" type="radio" name="selected_price"
                                        value="1" @if ($sparepart->selected_price == 1) checked @endif
                                        onchange="document.forms['form{{ $key }}'].submit();">
                                    <label for="radio-1{{ $key }}">&nbsp</label>
                                </div>
                            </th>
                        @endif
                        @if (Auth::user()->GroupId == 5)
                            <th class="min">
                                <div class="form_radio_btn mrk">
                                    <input id="radio-1{{ $key }}" type="radio" name="selected_mrk"
                                        value="1" @if ($sparepart->selected_mrk == 1) checked @endif
                                        onchange="document.forms['form{{ $key }}'].submit();">
                                    <label for="radio-1{{ $key }}">&nbsp</label>
                                </div>
                            </th>
                        @endif
                        <input type="hidden" name="price_original" id='price_original-{{ $key }}'
                            value='{{ $sparepart->price_original }}'>
                        <td id="td_price_analogue-{{ $key }}" scope="col"
                            class="min 
      @if ($sparepart->selected_mrk == 2 && $sparepart->selected_price == 2) zapmrk @endif
      @if ($sparepart->selected_mrk == 2) mrk @endif
      @if ($sparepart->selected_price == 2) zap @endif
      "
                            @if (Auth::user()->GroupId != 6 && Auth::user()->GroupId != 5) colspan=2 @endif>
                            @if (old('price_analogue.' . $key) !== null)
                                {{ old('price_analogue.' . $key) }}
                            @else
                                {{ $sparepart->price_analogue }}
                            @endif
                        </td>
                        @if (Auth::user()->GroupId == 6)
                            <th class="min">
                                <div class="form_radio_btn zap">
                                    <input id="radio-2{{ $key }}" type="radio" name="selected_price"
                                        value="2" @if ($sparepart->selected_price == 2) checked @endif
                                        onchange="document.forms['form{{ $key }}'].submit();">
                                    <label for="radio-2{{ $key }}">&nbsp</label>
                                </div>
                            </th>
                        @endif
                        @if (Auth::user()->GroupId == 5)
                            <th class="min">
                                <div class="form_radio_btn mrk">
                                    <input id="radio-2{{ $key }}" type="radio" name="selected_mrk"
                                        value="2" @if ($sparepart->selected_mrk == 2) checked @endif
                                        onchange="document.forms['form{{ $key }}'].submit();">
                                    <label for="radio-2{{ $key }}">&nbsp</label>
                                </div>
                            </th>
                        @endif
                        <input type="hidden" name="price_analogue" id='price_analogue-{{ $key }}'
                            value='{{ $sparepart->price_analogue }}'>
                        <td id="td_price_used-{{ $key }}" scope="col"
                            class="min 
      @if ($sparepart->selected_mrk == 3 && $sparepart->selected_price == 3) zapmrk @endif
      @if ($sparepart->selected_mrk == 3) mrk @endif
      @if ($sparepart->selected_price == 3) zap @endif
      "
                            @if (Auth::user()->GroupId != 6 && Auth::user()->GroupId != 5) colspan=2 @endif>
                            @if (old('price_used.' . $key) !== null)
                                {{ old('price_used.' . $key) }}
                            @else
                                {{ $sparepart->price_used }}
                            @endif
                        </td>
                        @if (Auth::user()->GroupId == 6)
                            <th class="min">
                                <div class="form_radio_btn zap">
                                    <input id="radio-3{{ $key }}" type="radio" name="selected_price"
                                        value="3" @if ($sparepart->selected_price == 3) checked @endif
                                        onchange="document.forms['form{{ $key }}'].submit();">
                                    <label for="radio-3{{ $key }}">&nbsp</label>
                                </div>
                            </th>
                        @endif
                        @if (Auth::user()->GroupId == 5)
                            <th class="min">
                                <div class="form_radio_btn mrk">
                                    <input id="radio-3{{ $key }}" type="radio" name="selected_mrk"
                                        value="3" @if ($sparepart->selected_mrk == 3) checked @endif
                                        onchange="document.forms['form{{ $key }}'].submit();">
                                    <label for="radio-3{{ $key }}">&nbsp</label>
                                </div>
                            </th>
                        @endif
                        <td
                            class="
@if ($sparepart->selected_mrk == 4 && $sparepart->selected_price == 4) zapmrk @endif
      @if ($sparepart->selected_mrk == 4) mrk @endif
      @if ($sparepart->selected_price == 4) zap @endif
">
                            @if (Auth::user()->GroupId == 6)
                                <div class="form_radio_btn zap">
                                    <input id="radio-4{{ $key }}" type="radio" name="selected_price"
                                        value="4" @if ($sparepart->selected_price == 4) checked @endif
                                        onchange="document.forms['form{{ $key }}'].submit();">
                                    <label for="radio-4{{ $key }}">&nbsp</label>
                                </div>
                            @endif
                            @if (Auth::user()->GroupId == 5)
                                <div class="form_radio_btn mrk">
                                    <input id="radio-4{{ $key }}" type="radio" name="selected_mrk"
                                        value="4" @if ($sparepart->selected_mrk == 4) checked @endif
                                        onchange="document.forms['form{{ $key }}'].submit();">
                                    <label for="radio-4{{ $key }}">&nbsp</label>
                                </div>
                            @endif
                            </th>
                            <input type="hidden" name="price_used" id='price_used-{{ $key }}'
                                value='{{ $sparepart->price_used }}'>

                        <th scope="col">
                            @if (isset($delta_full[$key]))
                                <div
                                    class="
       @if ($delta_full[$key] < 0) alert alert-success
       @else
       alert alert-danger @endif
       ">
                                    {{ $delta_full[$key] }}
                                </div>
                            @endif
                        </th>
                        <th scope="col">
                            @if (isset($delta_half[$key]))
                                <div
                                    class="
       @if ($delta_half[$key] < 0) alert alert-success
       @else
       alert alert-danger @endif
       ">
                                    {{ $delta_half[$key] }}
                                </div>
                            @endif
                            </td>
                        <td id="td_comment-{{ $key }}" scope="col" class="comment">
                            @if (old('comment.' . $key) !== null)
                                {{ old('comment.' . $key) }}
                            @else
                                {{ $sparepart->comment }}
                            @endif
                        </td>
                        <input type="hidden" name="comment" id='comment-{{ $key }}'
                            value='{{ $sparepart->comment }}'>
                        @if (session('rout') == 'directions')
                            <td id="td_status-{{ $key }}" scope="col">
                                @if (old('status.' . $key) !== null)
                                    {{ old('status.' . $key) }}
                                @else
                                    {{ $sparepart->status }}
                                @endif
                            </td>
                            <input type="hidden" name="status" id='status-{{ $key }}'
                                value='{{ $sparepart->status }}'>

                            <th id="td_date_price{{ $key }}" scope="col">
                                <input type="date" name="date_price" id='date_price{{ $key }}'
                                    @if (old('date_price.' . $key) != null) value='{{ date(old('date_price.' . $key)) }}'
     @else
         value='{{ date($sparepart->date_price) }}' @endif
                                    onchange="document.forms['form{{ $key }}'].submit();">

                            </th>
                            <th id="td_date_wait{{ $key }}" scope="col">

                                <div
                                    class="
        @if (strtotime(date('Y-m-d h:m:s')) > strtotime($sparepart->date_wait)) alert alert-danger @endif
        ">
                                    <input type="date" name="date_wait" id='date_wait{{ $key }}'
                                        @if (old('date_wait.' . $key) != null) value='{{ date(old('date_wait.' . $key)) }}'
     @else
         value='{{ date($sparepart->date_wait) }}' @endif
                                        onchange="document.forms['form{{ $key }}'].submit();">
                                </div>
                            </th>
                        @endif
                        @if (Auth::user()->GroupId == 5)
                            <th scope="col"> <a
                                    onclick="if (!confirm('Точно удалить запчасть <{{ $sparepart->name }}?>')) return false"
                                    href="{{ route('SparePart.destroy', $sparepart->id) }}" class="nav-link"><img
                                        src="/storage/img/delete.png" width="30" alt="delete"></a>
                            </th>
                        @endif
                    </form>
                </tr>
            @endforeach
            <tr>
                <form action="/SpareParts/store" name="callback" method="POST">
                    @csrf
                    @error('name')
                        <p class="text-red-500">{{ $message }}</p>
                    @enderror

                    <input name = "id_application" type="hidden" value="{{ $id }}">
                    <input name = "author" type="hidden" value="{{ Auth::user()->GroupId }}">
                    <th>
                        <a class="nav-link" href="#">
                            <img src="/storage/img/edit.png" width="30" alt="edit"
                                onclick="document.forms['callback'].submit();">
                        </a>

                    </th>
                    <th>
                        <input type="text" name="name" id="name" class="form-control">
                    </th>
                    <th>

                        <input type="text" name="part_number" id="part_number" class="form-control">

                    </th>
                    <th>

                        <input type="text" name="order_number" id="order_number" class="form-control">
                    </th>
                    <th>

                        <input type="text" name="price_ic_full" id="price_ic_full" class="form-control">
                    </th>
                    <th>

                        <input type="text" name="price_ic_half" id="price_ic_half" class="form-control">

                    </th>
                    <th colspan=2>

                        <input type="text" name="price_original" id="price_original" class="form-control">

                    </th>
                    <th colspan=2>
                        <input type="text" name="price_analogue" id="price_analogue" class="form-control">

                    </th>
                    <th colspan=2>
                        <input type="text" name="price_used" id="price_used" class="form-control">
                    </th>
                    <th>
                        <input type="text" name="comment" id="comment" class="form-control">

                    </th>
                    <th>
                    </th>
                    <th>
                    </th>
                    <th>
                    </th>
                    @if (session('rout') == 'directions')
                        <th>

                            <input type="text" name="status" id="status" class="form-control">

                        </th>
                        <th>
                            <input type="date" name="date_price" id="date_price" class="form-control">
                        </th>
                        <th>
                            <input type="date" name="date_wait" id="date_wait" class="form-control">

                        </th>
                    @endif

                </form>
            </tr>
        </tbody>
    </table>
    @if (isset($estimate->price_sp_full))
        <table @can('admin')
            id='demo'
        @endcan
            class="table table-striped-columns table-hover table-group-divider table-responsive">

            <tr>
                <th>Cтоимость узлов и деталей:</th>
                <td>
                    {{ $estimate->price_sp_full }}
                </td>
            </tr>
            <tr>
                <th>Cтоимость узлов и деталей c учетом износа:</th>
                <td>
                    {{ $estimate->price_sp_half }}
                </td>
            </tr>

            <tr>
                <th>Cтоимость ремонта/замены:</th>
                <td>
                    {{ $estimate->price_repair }}
                </td>
            </tr>
            <tr>
                <th>Cтоимость окраски контроля: </th>
                <td>
                    {{ $estimate->price_paint }}
                </td>
            </tr>
            <tr>
                <th>Cтоимость расходных материалов: </th>
                <td>
                    {{ $estimate->price_zip }}
                </td>
            </tr>
            <tr>
                <th>Cтоимость работ и материалов: </th>
                <td>
                    {{ $estimate->price_work }}
                </td>
            </tr>
            <tr>
                <th>Cтоимость восстановительного ремонта (Всего) </th>
                <td>
                    {{ $estimate->price_repair_full }}
                </td>
            </tr>
            <tr>
                <th>Затраты на восстановительный ремонт (с учетом износа): </th>
                <td>
                    {{ $estimate->price_repair_half }} </td>
            </tr>
            <tr>
                <th>Фактическая стоимость восстановительного ремонта </th>
                <td>
                    {{ $summ_order + $summ_service }} </td>
            </tr>
            <tr>
                <th>Разница от полной стоимости</th>
                <th>
                    <div
                        class="
                    @if ($summ_order + $summ_service - (int) str_replace(' ', '', $estimate->price_repair_full) < 0) alert alert-success
                    @else
                        alert alert-danger @endif
                    ">
                        {{ $summ_order + $summ_service - (int) str_replace(' ', '', $estimate->price_repair_full) }}
                    </div>
                </th>
            </tr>
            <tr>
                <th>Разница от стоимости с учетом износа</th>
                <th>
                    <div
                        class="
                    @if ($summ_order + $summ_service - (int) str_replace(' ', '', $estimate->price_repair_half) < 0) alert alert-success
                    @else
                        alert alert-danger @endif
                    ">
                        {{ $summ_order + $summ_service - (int) str_replace(' ', '', $estimate->price_repair_half) }}
                    </div>
                </th>
            </tr>

        </table>
    @endif
    <script>
        function tableSearch(k, table) {
            var phrase = document.getElementById('search-text' + k);
            var table = document.getElementById(table);
            var regPhrase = new RegExp(phrase.value, 'i');
            var flag = false;
            for (var i = 3; i < table.rows.length; i++) {
                flag = false;
                flag = regPhrase.test(table.rows[i].cells[k].innerHTML);
                if (flag) {
                    table.rows[i].style.display = "";
                } else {
                    table.rows[i].style.display = "none";
                }

            }
        }
        // ИНИЦИАЛИЗАЦИЯ - ДВОЙНОЙ ЩЕЛЧОК ДЛЯ РЕДАКТИРОВАНИЯ ЯЧЕЙКИ
        window.addEventListener("DOMContentLoaded", () => {
            for (let cell of document.querySelectorAll("#demo td")) {
                cell.ondblclick = () => {
                    editable.edit(cell);
                };
            }
        });
        let editable = {
            // "ПРЕОБРАЗОВАТЬ" В РЕДАКТИРОВАННУЮ ЯЧЕЙКУ
            edit: (cell) => {
                // УДАЛИТЬ "ДВОЙНОЙ ЩЕЛЧОК ДЛЯ РЕДАКТИРОВАНИЯ"
                cell.ondblclick = "";
                // РЕДАКТИРУЕМОЕ СОДЕРЖИМОЕ
                cell.contentEditable = true;
                cell.focus();
                // "ОТМЕТИТЬ" ТЕКУЩУЮ ВЫБРАННУЮ ЯЧЕЙКУ
                cell.classList.add("edit");
                editable.selected = cell;
                // НАЖМИТЕ ENTER ИЛИ ЩЕЛКНИТЕ ВНЕ, ЧТОБЫ ЗАВЕРШИТЬ РЕДАКТИРОВАНИЕ
                window.addEventListener("click", editable.close);
                cell.onkeydown = (evt) => {
                    if (evt.key == "Enter") {
                        editable.close(true);
                        return false;
                    }
                };
            },
            // ЗАВЕРШИТЬ "РЕЖИМ РЕДАКТИРОВАНИЯ"
            selected: null, // текущая выбранная ячейка
            close: (evt) => {
                if (evt.target != editable.selected) {

                    // УДАЛИТЬ "РЕДАКТИРОВАНИЕ"
                    window.getSelection().removeAllRanges();
                    editable.selected.contentEditable = false;
                    // ВОССТАНОВИТЬ КЛИК-СЛУШАТЕЛИ
                    window.removeEventListener("click", editable.close);

                    let cell = editable.selected;
                    cell.ondblclick = () => {
                        editable.edit(cell);
                    };
                    // "СНЯТЬ ПОМЕТКУ" ТЕКУЩЕЙ ВЫБРАННОЙ ЯЧЕЙКИ
                    editable.selected.classList.remove("edit");
                    editable.selected = null;
                    // newvar = "Третий способ вывода переменной";
                    let str = cell.id;
                    let start = str.indexOf("_");
                    let isid = document.getElementById(str.substring(start + 1, str.length));
                    start = str.indexOf("-");
                    let id = str.substring(start + 1, str.length);
                    isid.value = cell.innerHTML;
                    let form = document.getElementById('form' + id);
                    form.submit();
                }
            }
        };
    </script>
@endsection
