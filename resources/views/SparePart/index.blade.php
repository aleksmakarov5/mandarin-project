@extends('header_menu')
@section('title')
    Запчасти
@endsection
@section('main')
    {{-- {{ session('GroupId') }} --}}

    </div>
    <button class="accordion-est">
        Запчасти
    </button>

    @can('company')
        <a href="/SparePart/{{ $id }}/doc_load" class="nav-link">Загрузить данные из калькуляции</a>
    @endcan
    <table id="demo" class="editable">
        <thead>

            <tr>
                <th scope="col">#</th>
                <th scope="col">Наименование</th>
                <th scope="col">Кат. номер</th>
                <th scope="col">Зак. номер</th>
                @can('company')
                    <th scope="col">Цена СК</th>
                    <th scope="col">Цена СТО</th>

                    <th scope="col" colspan="2">Оригинал</th>
                    <th scope="col" colspan="2">Аналог</th>
                    <th scope="col" colspan="2">Б/у</th>

                    <th scope="col">-</th>
                    <th scope="col">Разница </th>
                @endcan
                <th scope="col" @if (session('GroupId') != 9) colspan="2" @endif>Цена СТО</th>

                @if (session('GroupId') == 9)
                    <th scope="col" colspan="1">Вариант СТО</th>
                @endif
                <th scope="col">Комментарий</th>
                <th scope="col">Статус</th>
                <th scope="col">Дата заказа</th>
                <th scope="col">Ожидаемая дата</th>




            </tr>

            @can('company')
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Итого</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col">{{ $summ_ic_full }}</th>
                    <th scope="col">{{ $summ_sto }}</th>
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

                    <th scope="col" colspan=2></th>
                    @if (session('rout') == 'applications')
                        <th scope="col"></th>


                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    @endif
                </tr>
            @endcan
            {{-- <tr>
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
                <th scope="col" colspan=6></th>




            </tr> --}}

        </thead>
        <tbody>



            @foreach ($spareparts as $key => $sparepart)
                <tr @if ($sparepart->author == 5) class="pink" @endif
                    @if ($sparepart->author == 9) class="sto" @endif>
                    <form name="form{{ $key }}" id='form{{ $key }}'
                        action="/SparePart/{{ $id }}/update" method="POST">

                        @csrf
                        <input type="hidden" name="id_application" value='{{ $id }}'>
                        <input type="hidden" name="id" id='id-{{ $key }}' value='{{ $sparepart->id }}'>
                        <input type="hidden" name="name" id='name-{{ $key }}' value='{{ $sparepart->name }}'>
                        @canany(['mrk', 'admin', 'spm'])
                            <input type="hidden" name="part_number" id='part_number-{{ $key }}'
                                value='{{ $sparepart->part_number }}'>
                            <input type="hidden" name="order_number" id='order_number{{ $key }}'
                                value='{{ $sparepart->order_number }}'>
                            <input type="hidden" name="price_ic_full" id='price_ic_full-{{ $key }}'
                                value='{{ isset($estimate->emr10) && $estimate->emr10 ? (float) $sparepart->price_ic_full * 1.1 : (float) $sparepart->price_ic_full }}'>
                            <input type="hidden" name="price_sto" id='price_sto-{{ $key }}'
                                value='{{ isset($estimate->emr10) && $estimate->emr10 ? (float) $sparepart->price_sto * 1.1 : (float) $sparepart->price_sto }}'>
                            <input type="hidden" name="price_original" id='price_original-{{ $key }}'
                                value='{{ $sparepart->price_original }}'>
                            <input type="hidden" name="price_analogue" id='price_analogue-{{ $key }}'
                                value='{{ $sparepart->price_analogue }}'>
                            <input type="hidden" name="price_used" id='price_used-{{ $key }}'
                                value='{{ $sparepart->price_used }}'>
                        @endcanany
                        @can('service')
                            <input type="hidden" name="price_service" id='price_service-{{ $key }}'
                                value='{{ $sparepart->price_used }}'>
                        @endcan
                        <td scope="col" id="td_id-{{ $key }}">{{ $key + 1 }}
                            <a name="{{ $sparepart->id }}"></a>
                        </td>
                        <td id="td_name-{{ $key }}" scope="col">
                            @if (old('name.' . $key) !== null)
                                {{ old('name.' . $key) }}
                            @else
                                {{ $sparepart->name }}
                            @endif
                        </td>
                        <td id="td_part_number-{{ $key }}" scope="col">
                            @if (old('part_number.' . $key) !== null)
                                {{ old('part_number.' . $key) }}
                            @else
                                {{ $sparepart->part_number }}
                            @endif
                        </td>
                        <td id="td_order_number{{ $key }}" scope="col">
                            @if (old('order_number.' . $key) !== null)
                                {{ old('order_number.' . $key) }}
                            @else
                                {{ $sparepart->order_number }}
                            @endif
                        </td>
                        @can('company')
                            <td id="td_price_ic_full-{{ $key }}" scope="col" class="ic_price">
                                @if (old('price_ic_full.' . $key) !== null)
                                    {{ old('price_ic_full.' . $key) }}
                                @else
                                    {{ round(isset($estimate->emr10) && $estimate->emr10 ? (float) $sparepart->price_ic_full * 1.1 : (float) $sparepart->price_ic_full) }}
                                @endif
                            </td>
                            <td id="td_price_sto-{{ $key }}" scope="col" class="ic_price">
                                @if (old('price_sto.' . $key) !== null)
                                    {{ old('price_sto.' . $key) }}
                                @else
                                    @if ($sparepart->price_sto == 0)
                                        {{ round(isset($estimate->emr10) && $estimate->emr10 ? (float) $sparepart->price_ic_full * 1.1 : (float) $sparepart->price_ic_full) }}
                                    @else
                                        {{ round(isset($estimate->emr10) && $estimate->emr10 ? (float) $sparepart->price_sto * 1.1 : (float) $sparepart->price_sto) }}
                                    @endif
                                @endif
                            </td>


                            <td id="td_price_original-{{ $key }}" scope="col"
                                class="min
      @if ($sparepart->selected_mrk == 1 && $sparepart->selected_price == 1 && $sparepart->selected_service == 1) zapmrksto @endif
      @if ($sparepart->selected_mrk == 1 && $sparepart->selected_price == 1 && $sparepart->selected_service != 1) zapmrk @endif
      @if ($sparepart->selected_mrk == 1 && $sparepart->selected_price != 1 && $sparepart->selected_service == 1) mrksto @endif
      @if ($sparepart->selected_mrk != 1 && $sparepart->selected_price == 1 && $sparepart->selected_service == 1) zapsto @endif
      @if ($sparepart->selected_mrk == 1) mrk @endif
      @if ($sparepart->selected_price == 1) zap @endif
      @if ($sparepart->selected_service == 1) sto @endif
      "
                                @if (session('GroupId') != 6 && session('GroupId') != 5 && session('GroupId') != 9) colspan=2 @endif>
                                @if (old('price_original.' . $key) !== null)
                                    {{ old('price_original.' . $key) }}
                                @else
                                    {{ $sparepart->price_original }}
                                @endif
                            </td>
                            @if (session('GroupId') == 6)
                                <th class="min">
                                    <div class="form_radio_btn zap">
                                        <input id="radio-1{{ $key }}" type="radio" name="selected_price"
                                            value="1" @if ($sparepart->selected_price == 1) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-1{{ $key }}">&nbsp</label>
                                    </div>
                                </th>
                            @endif
                            @if ((int) session('GroupId') == 5)
                                <th class="min">
                                    <div class="form_radio_btn mrk">
                                        <input id="radio-1{{ $key }}" type="radio" name="selected_mrk"
                                            value="1" @if ($sparepart->selected_mrk == 1) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-1{{ $key }}">&nbsp</label>
                                    </div>
                                </th>
                            @endif
                            @if (session('GroupId') == 9)
                                <th class="min">
                                    <div class="form_radio_btn sto">
                                        <input id="radio-1{{ $key }}" type="radio" name="selected_service"
                                            value="1" @if ($sparepart->selected_service == 1) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-1{{ $key }}">&nbsp</label>
                                    </div>
                                </th>
                            @endif
                            <td id="td_price_analogue-{{ $key }}" scope="col"
                                class="min 
     @if ($sparepart->selected_mrk == 2 && $sparepart->selected_price == 2 && $sparepart->selected_service == 2) zapmrksto @endif
      @if ($sparepart->selected_mrk == 2 && $sparepart->selected_price == 2 && $sparepart->selected_service != 2) zapmrk @endif
      @if ($sparepart->selected_mrk == 2 && $sparepart->selected_price != 2 && $sparepart->selected_service == 2) mrksto @endif
      @if ($sparepart->selected_mrk != 2 && $sparepart->selected_price == 2 && $sparepart->selected_service == 2) zapsto @endif
      @if ($sparepart->selected_mrk == 2) mrk @endif
      @if ($sparepart->selected_price == 2) zap @endif
      @if ($sparepart->selected_service == 2) sto @endif
      "
                                @if (session('GroupId') != 6 && session('GroupId') != 5 && session('GroupId') != 9) colspan=2 @endif>
                                @if (old('price_analogue.' . $key) !== null)
                                    {{ old('price_analogue.' . $key) }}
                                @else
                                    {{ $sparepart->price_analogue }}
                                @endif
                            </td>
                            @if (session('GroupId') == 6)
                                <th class="min">
                                    <div class="form_radio_btn zap">
                                        <input id="radio-2{{ $key }}" type="radio" name="selected_price"
                                            value="2" @if ($sparepart->selected_price == 2) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-2{{ $key }}">&nbsp</label>
                                    </div>
                                </th>
                            @endif
                            @if (session('GroupId') == 5)
                                <th class="min">
                                    <div class="form_radio_btn mrk">
                                        <input id="radio-2{{ $key }}" type="radio" name="selected_mrk"
                                            value="2" @if ($sparepart->selected_mrk == 2) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-2{{ $key }}">&nbsp</label>
                                    </div>
                                </th>
                            @endif
                            @if (session('GroupId') == 9)
                                <th class="min">
                                    <div class="form_radio_btn sto">
                                        <input id="radio-2{{ $key }}" type="radio" name="selected_service"
                                            value="2" @if ($sparepart->selected_service == 2) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-2{{ $key }}">&nbsp</label>
                                    </div>
                                </th>
                            @endif
                            <td id="td_price_used-{{ $key }}" scope="col"
                                class="min 
      @if ($sparepart->selected_mrk == 3 && $sparepart->selected_price == 3 && $sparepart->selected_service == 3) zapmrksto @endif
      @if ($sparepart->selected_mrk == 3 && $sparepart->selected_price == 3 && $sparepart->selected_service != 3) zapmrk @endif
      @if ($sparepart->selected_mrk == 3 && $sparepart->selected_price != 3 && $sparepart->selected_service == 3) mrksto @endif
      @if ($sparepart->selected_mrk != 3 && $sparepart->selected_price == 3 && $sparepart->selected_service == 3) zapsto @endif
      @if ($sparepart->selected_mrk == 3) mrk @endif
      @if ($sparepart->selected_price == 3) zap @endif
      @if ($sparepart->selected_service == 3) sto @endif
      "
                                @if (session('GroupId') != 6 && session('GroupId') != 5 && session('GroupId') != 9) colspan=2 @endif>
                                @if (old('price_used.' . $key) !== null)
                                    {{ old('price_used.' . $key) }}
                                @else
                                    {{ $sparepart->price_used }}
                                @endif
                            </td>
                            @if (session('GroupId') == 6)
                                <th class="min">
                                    <div class="form_radio_btn zap">
                                        <input id="radio-3{{ $key }}" type="radio" name="selected_price"
                                            value="3" @if ($sparepart->selected_price == 3) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-3{{ $key }}">&nbsp</label>
                                    </div>
                                </th>
                            @endif
                            @if (session('GroupId') == 5)
                                <th class="min">
                                    <div class="form_radio_btn mrk">
                                        <input id="radio-3{{ $key }}" type="radio" name="selected_mrk"
                                            value="3" @if ($sparepart->selected_mrk == 3) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-3{{ $key }}">&nbsp</label>
                                    </div>
                                </th>
                            @endif
                            @if (session('GroupId') == 9)
                                <th class="min">
                                    <div class="form_radio_btn sto">
                                        <input id="radio-3{{ $key }}" type="radio" name="selected_service"
                                            value="3" @if ($sparepart->selected_service == 3) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-3{{ $key }}">&nbsp</label>
                                    </div>
                                </th>
                            @endif

                            <th
                                class="
@if ($sparepart->selected_mrk == 4 && $sparepart->selected_price == 4) zapmrk @endif
      @if ($sparepart->selected_mrk == 4) mrk @endif
      @if ($sparepart->selected_price == 4) zap @endif
">
                                @if (session('GroupId') == 6)
                                    <div class="form_radio_btn zap">
                                        <input id="radio-4{{ $key }}" type="radio" name="selected_price"
                                            value="4" @if ($sparepart->selected_price == 4) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-4{{ $key }}">&nbsp</label>
                                    </div>
                                @endif
                                @if (session('GroupId') == 5)
                                    <div class="form_radio_btn mrk">
                                        <input id="radio-4{{ $key }}" type="radio" name="selected_mrk"
                                            value="4" @if ($sparepart->selected_mrk == 4) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-4{{ $key }}">&nbsp</label>
                                    </div>
                                @endif
                            </th>

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
                        @endcan
                        <td id="td_price_service-{{ $key }}" scope="col"
                            class="min 
      @if ($sparepart->selected_mrk > 0 && $sparepart->selected_mrk < 4) @if ($sparepart->approved) zap 
      @else
      na @endif @endif
      "
                            @if (session('GroupId') != 5 && session('GroupId') != 9) colspan=2 @endif>
                            @if (old('price_service.' . $key) !== null)
                                {{ old('price_service.' . $key) }}
                            @else
                                {{ $sparepart->price_service }}
                            @endif
                        </td>
                        @if (session('GroupId') == 9)
                            <td id="td_price_service-{{ $key }}" scope="col"
                                class="min 
      @if ($sparepart->approved) zap 
      @else
      na @endif
      ">

                                <select class=sel id="selected_service" name="selected_service"
                                    onchange="document.forms['form{{ $key }}'].submit();">

                                    <option value=1 @if ($sparepart->selected_service == 1) selected @endif>Оригинал
                                    </option>

                                    <option value=2 @if ($sparepart->selected_service == 2) selected @endif>Аналог</option>

                                    <option value=3 @if ($sparepart->selected_service == 3) selected @endif>Б/У</option>

                                </select>


                            </td>
                        @endif

                        @if (session('GroupId') == 5)
                            <th class="min">
                                @if ($sparepart->selected_mrk > 0 && $sparepart->selected_mrk < 4)
                                    <div class="form_radio_btn zap">
                                        <input id="radio-5{{ $key }}" type="radio" name="approved"
                                            value="1" @if ($sparepart->approved) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-5{{ $key }}">+</label>
                                    </div>
                                    <div class="form_radio_btn na">
                                        <input id="radio-6{{ $key }}" type="radio" name="approved"
                                            value="0" @if (!$sparepart->approved) checked @endif
                                            onchange="document.forms['form{{ $key }}'].submit();">
                                        <label for="radio-6{{ $key }}">-</label>
                                    </div>
                                @endif
                            </th>
                        @endif


                        <td id="td_comment-{{ $key }}" scope="col" class="comment">
                            @if (old('comment.' . $key) !== null)
                                {{ old('comment.' . $key) }}
                            @else
                                {{ $sparepart->comment }}
                            @endif
                        </td>
                        <input type="hidden" name="comment" id='comment-{{ $key }}'
                            value='{{ $sparepart->comment }}'>
                        @if (session('rout') == 'applications')
                            <td id="td_status_id-{{ $key }}" scope="col"
                                @switch($sparepart->status_id)
                                @case(1)
                                    class='yellow'
                                    @break
                                @case(2)
                                    class='green'
                                    @break
                                  @case(3)
                                    class='teal'
                                    @break  
                                    @case(4)
                                    class='blue'
                                    @break
                                @default
                                class='gray'
                                    
                            @endswitch>
                                @if (session('GroupId') == 6 || session('GroupId') == 5)


                                    <select id="status_id" name="status_id"
                                        onchange="document.forms['form{{ $key }}'].submit();">
                                        <option value="0" @if ($sparepart->status_id == null) selected @endif>
                                            -------
                                        </option>
                                        @foreach ($statuses as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id === $sparepart->status_id) selected @endif>
                                                @if (isset($item->StatusName))
                                                    {{ $item->StatusName }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    @if (isset($item->StatusName))
                                        {{ $item->StatusName }}
                                    @endif
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
                        @if (session('GroupId') == 5 || session('GroupId') == 6 || session('GroupId') == $sparepart->author)
                            <th scope="col"> <a
                                    onclick="if (!confirm('Точно удалить запчасть <{{ $sparepart->name }}?>')) return false"
                                    href="{{ route('SparePart.destroy', $sparepart->id) }}" class="nav-link"><img
                                        src="/storage/img/delete.png" width="20" alt="delete"></a>
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
                    <input name = "author" type="hidden" value="{{ session('GroupId') }}">
                    <th>
                        <a class="nav-link" href="#">
                            <img src="/storage/img/edit.png" width="30" alt="edit"
                                onclick="document.forms['callback'].submit();">
                        </a>

                    </th>
                    <th>
                        <div class=" @error('name') alert alert-danger @enderror">
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}">
                        </div>
                    </th>
                    <th>

                        <input type="text" name="part_number" id="part_number" class="form-control"
                            value="{{ old('part_number') }}">

                    </th>
                    <th>

                        <input type="text" name="order_number" id="order_number" class="form-control"
                            value="{{ old('order_number') }}">
                    </th>
                    @can('company')
                        <th>

                            <input type="text" name="price_ic_full" id="price_ic_full" class="form-control"
                                value="{{ old('price_ic_full') }}">
                        </th>
                    @endcan

                    <th colspan=2>

                        <input type="text" name="price_original" id="price_original" class="form-control"
                            value="{{ old('price_original') }}">

                    </th>
                    <th colspan=2>
                        <input type="text" name="price_analogue" id="price_analogue" class="form-control"
                            value="{{ old('price_analogue') }}">

                    </th>
                    <th colspan=2>
                        <input type="text" name="price_used" id="price_used" class="form-control"
                            value="{{ old('price_used') }}">
                    </th>
                    @can('company')
                        <th colspan=4></th>
                    @endcan
                    <th>
                        <input type="text" name="comment" id="comment" class="form-control"
                            value="{{ old('comment') }}">


                    </th>

                    @if (session('rout') == 'applications')
                        <th>

                            <input type="text" name="status" id="status" class="form-control"
                                value="{{ old('status') }}">

                        </th>
                        <th>
                            <input type="date" name="date_price" id="date_price" class="form-control"
                                value="{{ old('date_price') }}">
                        </th>
                        <th>
                            <input type="date" name="date_wait" id="date_wait" class="form-control"
                                value="{{ old('date_wait') }}">

                        </th>
                    @endif

                </form>
            </tr>
        </tbody>
    </table>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @can('company')
        <button class="accordion-est">
            Работы по калькуляции
        </button>
        <div class="panel" style="max-height: 10008px;">
            <table class="editable" id='demo'>
                <thead class="accordion-est">
                    <tr>
                        <th scope="col" colspan=4>Данные из калькуляции</th>
                        <th scope="col" colspan=4>
                            <a href="/Work/{{ $id }}/doc_load" class="nav-link">Загрузить</a>
                        </th>



                    </tr>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Наименование</th>

                        <th scope="col">Цена страховой</th>
                        <th scope="col">Цена сервиса</th>
                        {{-- <th scope="col">Цена сервиса</th> --}}
                        <th scope="col"></th>

                    </tr>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Итого</th>
                        <th scope="col">{{ $summ_ic }}</th>
                        {{-- <th scope="col">{{$summ_service}}</th> --}}
                        <th scope="col"></th>
                        <th scope="col"></th>

                    </tr>
                </thead>
                <tbody>



                    @foreach ($Works as $key => $Work)
                        <tr>
                            <form name="form{{ $key }}" id='form{{ $key }}'
                                action="/Work/{{ $Work->id }}/update" method="POST">

                                @csrf
                                <input type="hidden" name="id_application" value='{{ $id }}'>

                                <td scope="col" id="td_id-{{ $key }}">
                                    {{ $key + 1 }}
                                    <a name="{{ $Work->id }}"></a>
                                </td>

                                <input type="hidden" name="id" id='id-{{ $key }}'
                                    value=' {{ $Work->id }}'>
                                <td scope="col" id="td_name-{{ $key }}">{{ $Work->name }}</td>
                                <input type="hidden" name="name" id='name-{{ $key }}'
                                    value='{{ $Work->name }}'>

                                <td scope="col" id="td_summ-{{ $key }}">{{ $Work->summ }}</td>

                                <input type="hidden" name="summ" id='summ-{{ $key }}'
                                    value='{{ $Work->summ }}'>
                                <td scope="col" id="td_price_service-{{ $key }}">{{ $Work->price_service }}
                                </td>
                                <input type="hidden" name="price_service" id='price_service-{{ $key }}'
                                    value='{{ $Work->price_service }}'>
                                <td scope="col">
                                    @if (Auth::user()->GroupId == 5 || Auth::user()->GroupId == $Work->author)
                                        <a onclick="if (!confirm('Точно удалить работу <{{ $Work->name }}?>')) return false"
                                            href="{{ route('Work.destroy', $Work->id) }}" class="nav-link"><img
                                                src="/storage/img/delete.png" width="20" alt="delete"></a>
                                    @endif
                                </td>
                        </tr>
                        </form>
                    @endforeach
                    <tr>

                        <form action="/Works/store" method="POST">
                            @csrf
                            <th>
                                <input name = "id_application" type="hidden" value="{{ $id }}">
                            </th>
                            <th>
                                <div class="form-floating">

                                    <input type="text" name="name" id="name" class="form-control">

                                </div>
                            </th>
                            <th>

                                <div class="form-floating">
                                    <input type="text" name="summ" id="summ" class="form-control" value=0>

                                </div>

                            </th>
                            <th>
                                <input type="submit" value="Создать" class="nav-link">
                            </th>
                        </form>
                    </tr>
                </tbody>
            </table>
        </div>
    @endcan
    <button class="accordion-est">
        Фактические работы
    </button>
    <div class="panel" style="max-height: 10008px;">
        <table class="editable" id='demo'>
            <thead>
                <tr>
                    <th scope="col" colspan=4>Фактические работы</th>



                </tr>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Наименование</th>

                    {{-- <th scope="col">Сумма</th> --}}
                    <th scope="col">Цена сервиса</th>
                    <th scope="col">
                        @can('company')
                            Разница стоимости
                        @endcan
                    </th>

                </tr>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Итого</th>
                    {{-- <th scope="col">{{$summ_ic}}</th> --}}
                    <th scope="col">{{ $summ_service }}</th>
                    <th scope="col">
                        @can('company')
                            <div
                                class="
       @if ($delta < 0) alert alert-success
       @else
       alert alert-danger @endif
       ">

                                {{ $delta }}</div>
                        @endcan
                    </th>

                </tr>
            </thead>
            <tbody>



                @foreach ($Works_serv as $key => $Work)
                    <tr @if ($Work->author == 9) class="sto" @endif>
                        <form name="form-{{ $key }}" id='form-{{ $key }}'
                            action="/Work/{{ $Work->id }}/update" method="POST">

                            @csrf
                            <input type="hidden" name="id_application" value='{{ $id }}'>

                            <td scope="col" id="td_id--{{ $key }}">
                                {{ $key + 1 }}
                                <a name="{{ $Work->id }}"></a>
                            </td>

                            <input type="hidden" name="id" id='id--{{ $key }}'
                                value=' {{ $Work->id }}'>
                            <td scope="col" id="td_name--{{ $key }}">{{ $Work->name }}</td>
                            <input type="hidden" name="name" id='name--{{ $key }}'
                                value='{{ $Work->name }}'>

                            <td id="td_price_service--{{ $key }}" scope="col">{{ $Work->price_service }}
                            </td>
                            <input type="hidden" name="price_service" id='price_service--{{ $key }}'
                                value='{{ $Work->price_service }}'>
                            <input type="hidden" name="summ" id='summ--{{ $key }}'
                                value='{{ $Work->summ }}'>

                            <td scope="col">
                                @if (Auth::user()->GroupId == 5 || Auth::user()->GroupId == $Work->author)
                                    <a onclick="if (!confirm('Точно удалить работу <{{ $Work->name }}?>')) return false"
                                        href="{{ route('Work.destroy', $Work->id) }}" class="nav-link"><img
                                            src="/storage/img/delete.png" width="20" alt="delete">
                                    </a>
                                @endif
                            </td>
                    </tr>
                    </form>
                @endforeach
                <tr>

                    <form action="/Works/store" method="POST">
                        @csrf
                        <th>
                            <input name = "id_application" type="hidden" value="{{ $id }}">
                        </th>
                        <th>
                            <div class="form-floating">

                                <input type="text" name="name" id="name" class="form-control">

                            </div>

                            <div class="form-floating">
                                <input type="hidden" name="summ" id="summ" class="form-control" value=0>

                            </div>
                        </th>
                        <th>
                            <div class="form-floating">
                                <input type="text" name="price_service" id="price_service" class="form-control">

                                <input name = "author" type="hidden" value="{{ Auth::user()->GroupId }}">
                            </div>
                        </th>
                        <th>
                            <input type="submit" value="Создать" class="nav-link">
                        </th>
                    </form>
                </tr>
            </tbody>
        </table>
    </div>
    @can('company')
        <button class="accordion-est">
            Итого
        </button>
        <div class="panel" style="max-height: 10008px;">
            <table id='demo' class="table table-striped-columns table-hover table-group-divider table-responsive">
                <tr>
                    <form action="/sertificat/{{ $id }}" method="POST" id="form400" name="form400">
                        @csrf
                        <input type="hidden" id="sertificat-400" name="sertificat" value={{ $sertificat }}>
                        <th>Сертификат:</th>
                        <td id="td_sertificat-400">
                            @if (isset($sertificat))
                                {{ Number::Currency($sertificat, 'RUR', 'ru') }}
                            @endif
                        </td>
                    </form>
                </tr>
                <tr>

                </tr>
                <tr>
                    <form action="/emr10/{{ $id }}" method="POST" id="form_emr" name="form_emr">
                        @csrf
                        <th>ЕМР+10:</th>
                        <th>
                            <select class=sel id="emr10" name="emr10" onchange="document.forms['form_emr'].submit();">
                                <option value=0 @if (isset($estimate->emr10) && $estimate->emr10 == 0) selected @endif>нет</option>
                                <option value=1 @if (isset($estimate->emr10) && $estimate->emr10 == 1) selected @endif>да</option>
                            </select>
                        </th>
                    </form>
                </tr>
                <form action="/estimate/{{ $id }}" method="POST" id="form500" name="form500">
                    @csrf
                    <input type="hidden" id="price_sp_full-500" name="price_sp_full"
                        value=@if (isset($estimate->price_sp_full)) {{ $estimate->price_sp_full }} @endif>

                    <tr>
                        <th>Cтоимость узлов и деталей:</th>
                        <td id='td_price_sp_full-500'>
                            @if (isset($estimate->price_sp_full))
                                {{ Number::Currency(round(isset($estimate->emr10) && $estimate->emr10 ? (float) $estimate->price_sp_full * 1.1 : (float) $estimate->price_sp_full, 2), 'RUR', 'ru') }}
                            @endif
                        </td>
                    </tr>
                    <input type="hidden" id="price_sp_half-500" name="price_sp_half"
                        value=@if (isset($estimate->price_sp_half)) {{ $estimate->price_sp_half }} @endif>

                    <tr>
                        <th>Cтоимость узлов и деталей c учетом износа:</th>
                        <td id='td_price_sp_half-500'>
                            @if @if (isset($estimate->price_sp_half))
                                {{ Number::Currency(round(isset($estimate->emr10) && $estimate->emr10 ? (float) $estimate->price_sp_half * 1.1 : (float) $estimate->price_sp_half, 2), 'RUR', 'ru') }}
                            @endif
                        </td>
                    </tr>
                    <input type="hidden" id="price_repair-500" name="price_repair"
                        value=@if (isset($estimate->price_repair)) {{ $estimate->price_repair }} @endif>

                    <tr>
                        <th>Cтоимость ремонта/замены:</th>
                        <td id='td_price_repair-500'>
                            @if (isset($estimate->price_repair))
                                {{ Number::Currency(round(isset($estimate->emr10) && $estimate->emr10 ? (float) $estimate->price_repair * 1.1 : (float) $estimate->price_repair, 2), 'RUR', 'ru') }}
                            @endif
                        </td>
                    </tr>
                    <input type="hidden" id="price_paint-500" name="price_paint"
                        value=@if (isset($estimate->price_paint)) {{ $estimate->price_paint }} @endif>

                    <tr>
                        <th>Cтоимость окраски контроля: </th>
                        <td id='td_price_paint-500'>
                            @if (isset($estimate->price_paint))
                                {{ Number::Currency(round(isset($estimate->emr10) && $estimate->emr10 ? (float) $estimate->price_paint * 1.1 : (float) $estimate->price_paint, 2), 'RUR', 'ru') }}
                            @endif
                        </td>
                    </tr>
                    <input type="hidden" id="price_zip-500" name="price_zip"
                        value=@if (isset($estimate->price_zip)) {{ $estimate->price_zip }} @endif>

                    <tr>
                        <th>Cтоимость расходных материалов: </th>
                        <td id='td_price_zip-500'>
                            @if (isset($estimate->price_zip))
                                {{ Number::Currency(round(isset($estimate->emr10) && $estimate->emr10 ? (float) $estimate->price_zip * 1.1 : (float) $estimate->price_zip, 2), 'RUR', 'ru') }}
                            @endif
                        </td>
                    </tr>
                    <input type="hidden" id="price_work-500" name="price_work"
                        value=@if (isset($estimate->price_work)) {{ $estimate->price_work }} @endif>

                    <tr>
                        <th>Cтоимость работ и материалов: </th>
                        <td id='td_price_work-500'>
                            @if (isset($estimate->price_work))
                                {{ Number::Currency(round(isset($estimate->emr10) && $estimate->emr10 ? (float) $estimate->price_work * 1.1 : (float) $estimate->price_work, 2), 'RUR', 'ru') }}
                            @endif
                        </td>
                    </tr>
                    <input type="hidden" id="price_repair_full-500" name="price_repair_full"
                        value=@if (isset($estimate->price_repair_full)) {{ $estimate->price_repair_full }} @endif>

                    <tr>
                        <th>Cтоимость восстановительного ремонта (Всего) </th>
                        <td id='td_price_repair_full-500'>
                            @if (isset($estimate->price_repair_full))
                                {{ Number::Currency(round(isset($estimate->emr10) && $estimate->emr10 ? (float) $estimate->price_repair_full * 1.1 : (float) $estimate->price_repair_full, 2), 'RUR', 'ru') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Фактическая стоимость восстановительного ремонта </th>
                        <th>
                            @if (isset($summ_order) && isset($summ_service))
                                {{ Number::Currency($summ_order + $summ_service, 'RUR', 'ru') }}
                            @endif
                        </th>

                    </tr>
                    <tr>
                        <th>Разница от полной стоимости</th>
                        <th>
                            @if (isset($estimate->price_repair_full))
                                <div
                                    class="
                    @if ($summ_order + $summ_service - (int) str_replace(' ', '', $estimate->price_repair_full) < 0) alert alert-success
                    @else
                        alert alert-danger @endif
                    ">
                                    @if ($estimate->emr10)
                                        {{ Number::Currency($summ_order + $summ_service + $sertificat - (int) str_replace(' ', '', $estimate->price_repair_full) * 1.1, 'RUR', 'ru') }}
                                    @else
                                        {{ Number::Currency($summ_order + $summ_service + $sertificat - (int) str_replace(' ', '', $estimate->price_repair_full), 'RUR', 'ru') }}
                                    @endif

                                </div>
                            @endif
                        </th>
                    </tr>
                </form>

            </table>
        </div>
    @endcan
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
                    let params = new FormData(form);
                    let url = form.getAttribute('action');

                    fetch(url, {
                        method: 'POST',
                        body: params
                    });
                }
            }
        };

        var acc = document.getElementsByClassName("accordion-est");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
    </script>
@endsection
