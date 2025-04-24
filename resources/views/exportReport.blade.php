<table >
  <thead>

    <tr>
        <th rowspan=2 scope="col" valign=middle style="
        width: 190px;
border: 1px solid black;
font-size: 11pt;
text-align: left;
font-weight: normal;
font-family: Calibri;
">Каталожный номер <br>детали</th>
      <th rowspan=2 scope="col" valign=middle
      style="
        width: 230px;
border: 1px solid black;
font-size: 11pt;
text-align: left;
font-weight: normal;
font-family: Calibri;
">
Название детали</th>

      <th rowspan=2 scope="col" valign=middle style="
        width: 150px;
border: 1px solid black;
text-align: left;
font-size: 11pt;
text-align: left;
font-weight: normal;
font-family: Calibri;
">Прайс РСА <br> (актуальный на дату ДТП)</th>
      <th rowspan=2 valign=middle style="
        width: 180px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: orange;
">Среднерыночная <br> стоимость детали</th>
      <th rowspan=2 valign=middle style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: green;
">Цена СТОА <br>Нов. оригинал</th>
      <th valign=middle style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: blue;
">Цена СТОА (аналог) <br> Комбинир</th>
<th valign=middle style="
        width: 180px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: white;
"></th>
<th valign=middle style="
        width: 180px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: green;
">
Нов. Оригинал <br> (Выделить цветом)
</th>
<th rowspan=3 valign=middle style="
        width: 350px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: yellow;
">
    Образец заполнения для СТОА <br> При комбинированном ремонте заполняется <br> столбец согласно цветц применяемых З/Ч
</th>
</tr>
<tr>
    <th valign=middle style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: purple;
">Б/У</th>
<th valign=middle style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: white;
"></th>
<th valign=middle style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: blue;
">Аналог <br> (Выделить цветом)</th>
</tr>
<tr>
    <th scope="col" valign=middle style="
            width: 190px;
    border: 1px solid black;
    font-size: 11pt;
    text-align: left;
    font-weight: normal;
    font-family: Calibri;
    ">Заполняется СТОА</th>
    <th scope="col" valign=middle style="
            width: 230px;
    border: 1px solid black;
    font-size: 11pt;
    text-align: left;
    font-weight: normal;
    font-family: Calibri;
    ">
        Заполняется СТОА</th>

    <th scope="col" valign=middle style="
            width: 150px;
    border: 1px solid black;
    text-align: left;
    font-size: 11pt;
    text-align: left;
    font-weight: normal;
    font-family: Calibri;
    ">Заполняется СТОА</th>
    <th  valign=middle style="
            width: 180px;
    border: 1px solid black;
    font-size: 11pt;
    font-weight: bold;
    font-family: Calibri;
    text-align: center;
    background-color: orange;
    ">автоматический расчет</th>
    <th valign=middle style="
            width: 150px;
    border: 1px solid black;
    font-size: 11pt;
    font-weight: bold;
    font-family: Calibri;
    text-align: center;
    background-color: green;
    ">Заполняется СТОА</th>
    <th valign=middle style="
            width: 150px;
    border: 1px solid black;
    font-size: 11pt;
    font-weight: bold;
    font-family: Calibri;
    text-align: center;
    background-color: blue;
    ">Заполняется СТОА</th>
    <th valign=middle style="
            width: 180px;
    border: 1px solid black;
    font-size: 11pt;
    font-weight: bold;
    font-family: Calibri;
    text-align: center;
    background-color: white;
    "></th>
    <th valign=middle style="
            width: 180px;
    border: 1px solid black;
    font-size: 11pt;
    font-weight: bold;
    font-family: Calibri;
    text-align: center;
    background-color: purple;
    ">
        Б/У <br> (Выделить цветом)
    </th>

</tr>
  </thead>
  <tbody>


@php
$i = 1;
@endphp
@foreach ($spareparts as $sparepart)
    @if (($sparepart->price_sto != 0) && ((int) $sparepart->price_sto != (int) $sparepart->price_ic_full))
@php
        $i++;
@endphp

            <tr>

                  <td  style="
        border: 1px solid black;
        font-size: 11pt;
        font-family: Calibri;
        ">{{$sparepart->part_number}} </td>
                  <td style="
        border: 1px solid black;
        font-size: 11pt;
        font-family: Calibri;
        ">{{$sparepart->name}}</td>

                   <td style="
        border: 1px solid black;
        font-size: 11pt;
        font-family: Calibri;
        ">{{$sparepart->price_ic_full}}</td>
                  <td style="
        border: 1px solid black;
        font-size: 11pt;
        font-family: Calibri;
        " data-format  bgcolor="orange">0</td>
                  <td style="
        border: 1px solid black;
        font-size: 11pt;
        font-family: Calibri;
        " bgcolor="green"

        >{{$sparepart->price_original}}</td>
                  <td style="
        border: 1px solid black;
        font-size: 11pt;
        font-family: Calibri;
        "  bgcolor=
        @php
        if ($sparepart->selected_mrk == 2)
            echo "blue";
        elseif ($sparepart->selected_mrk == 3)
            echo "purple";
        else
            echo "green";
        @endphp
        >
        @php
        if ($sparepart->selected_mrk == 2)
            echo $sparepart->price_analogue;
        elseif ($sparepart->selected_mrk == 3)
            echo $sparepart->price_used;
        else
            echo $sparepart->price_original;
        @endphp
        </td>




                </tr>
    @endif
@endforeach

<tr>
    <td colspan="2"
    style="
    border: 3px solid black;
    font-size: 11pt;
    font-family: Calibri;
    font-weight: bold;
    text-align: center;
    "
    >Итого:</td>
    <td style="
    border: 3px solid black;
    font-size: 11pt;
    font-family: Calibri;
    font-weight: bold;
    text-align: center;
    ">@php
echo "=C4";
for ($j = 5; $j <= $i + 2; $j++)
    echo "+C" . $j;
    @endphp</td>
    <td style="
        border: 3px solid black;
        font-size: 11pt;
        font-family: Calibri;
        font-weight: bold;
        text-align: center;
    background-color: orange;
        ">@php
echo "=D4";
for ($j = 5; $j <= $i + 2; $j++)
    echo "+D" . $j;
        @endphp</td>
        <td style="
            border: 3px solid black;
            font-size: 11pt;
            font-family: Calibri;
            font-weight: bold;
            text-align: center;
    background-color: green;
            ">@php
echo "=E4";
for ($j = 5; $j <= $i + 2; $j++)
    echo "+E" . $j;
            @endphp</td>
            <td style="
                border: 3px solid black;
                font-size: 11pt;
                font-family: Calibri;
                font-weight: bold;
                text-align: center;
    background-color: blue;
                ">@php
echo "=F4";
for ($j = 5; $j <= $i + 2; $j++)
    echo "+F" . $j;
                @endphp</td>

</tr>
<tr></tr>
<tr>
    <td colspan="2" valign=middle  style="
                border: 3px solid black;
                font-size: 15pt;
                font-family: Calibri;
                font-weight: bold;
                text-align: center;
    background-color: white;
        height: 120px;
                ">
Необходимая доплата относительно <br> прайса РСА <br> (автоматический расчет)
    </td>
    <td valign=middle style="
                    border: 3px solid black;
                    font-size: 15pt;
                    font-family: Calibri;
                    font-weight: bold;
                    text-align: center;
        background-color: white;
                    ">
                    Руб
    </td>
    <td valign=middle style="
                        border: 3px solid black;
                        font-size: 15pt;
                        font-family: Calibri;
                        font-weight: bold;
                        text-align: center;
            background-color: white;
                        ">
        %
    </td>

</tr>
<tr>
    <td colspan="2" valign=middle style="
                    border: 3px solid black;
                    font-size: 12pt;
                    font-family: Calibri;
                    font-weight: bold;
                    text-align: right;
                    text-decoration: underline;
        background-color: orange;
        height: 70px;
                    ">
        По среднему рынку:
    </td>
    <td valign=middle style="
                        border: 3px solid black;
                        font-size: 15pt;
                        font-family: Calibri;
                        font-weight: bold;
                        text-align: center;
            background-color: orange;
                        ">
        =D{{$i + 3}}-C{{$i + 3}}
    </td>
    <td valign=middle style="
                            border: 3px solid black;
                            font-size: 15pt;
                            font-family: Calibri;
                            font-weight: bold;
                            text-align: center;
                background-color: orange;
                            ">
=D{{$i + 3}}/C{{$i + 3}}*100
    </td>
</tr>
<tr>
    <td colspan="2" valign=middle style="
                    border: 3px solid black;
                    font-size: 12pt;
                    font-family: Calibri;
                    font-weight: bold;
                    text-align: right;
                    text-decoration: underline;
        background-color: blue;
        height: 70px;
                    ">
        При ремонте новыми аналоговыми деталями или <br> комбинированным способом:
    </td>
    <td valign=middle style="
                        border: 3px solid black;
                        font-size: 15pt;
                        font-family: Calibri;
                        font-weight: bold;
                        text-align: center;
            background-color: blue;
                        ">
        =F{{$i + 3}}-C{{$i + 3}}
    </td>
    <td valign=middle style="
                            border: 3px solid black;
                            font-size: 15pt;
                            font-family: Calibri;
                            font-weight: bold;
                            text-align: center;
                background-color: blue;
                            ">
=F{{$i + 3}}/C{{$i + 3}}*100
    </td>
</tr>
<tr>
    <td colspan="2" valign=middle style="
                    border: 3px solid black;
                    font-size: 12pt;
                    font-family: Calibri;
                    font-weight: bold;
                    text-align: right;
                    text-decoration: underline;
        background-color: green;
        height: 70px;
                    ">
        При ремонте новыми оригинальными деталями или <br> по прайсу СТОА:
    </td>
    <td valign=middle style="
                        border: 3px solid black;
                        font-size: 15pt;
                        font-family: Calibri;
                        font-weight: bold;
                        text-align: center;
            background-color: green;
                        ">
        =E{{$i + 3}}-C{{$i + 3}}
    </td>
    <td valign=middle style="
                            border: 3px solid black;
                            font-size: 15pt;
                            font-family: Calibri;
                            font-weight: bold;
                            text-align: center;
                background-color: green;
                            ">
=E{{$i + 3}}/C{{$i + 3}}*100
    </td>
</tr>
<tr>
    <td colspan=2 rowspan=2 valign=top style="
                    border: 3px solid black;
                    font-size: 12pt;
                    font-family: Calibri;
                    font-weight: bold;
                    text-align: right;
height: 500px;
        background-color: white;
                    ">Примечание СТОА:</td>
        <td colspan=2 valign=top style="
                    border: 3px solid black;
                    font-size: 12pt;
                    font-family: Calibri;
                    font-weight: bold;
                    text-align: right;
height: 150px;
        background-color: white;
                    ">Заключение куратора СТОА:<br>
        Стоимость деталей по прайсу РСА<br>
        не соответствует среднерыночной стоимости З/Ч<br>
        СПАО "Ингосстрах"<br>
        УВТ ОРРСТОА:<br>
        ФИО</td>
</tr>
<tr>
    <td colspan=2 valign=top style="
                    border: 3px solid black;
                    font-size: 12pt;
                    font-family: Calibri;
                    font-weight: bold;
                    text-align: right;
height: 150px;
        background-color: white;
                    ">Заключение куратора убытка:<br>
    Согласована доплата относительно Среднего рынка/Нового аналога/Прайса СТОА (указать сумму доплаты)<br>
    СПАО "Ингосстрах"<br>
    УУ ОСАГО и ДГО ОРСП:<br>
    ФИО</td>
</tr>
 </tbody>
</table>
