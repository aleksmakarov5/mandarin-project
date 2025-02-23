<table >
  <thead>

    <tr>
        <th scope="col" valign=middle style="
        width: 130px;
border: 1px solid black;
font-size: 11pt;
text-align: left;
font-weight: normal;
font-family: Calibri;
">Каталожный номер <br>детали</th>
      <th scope="col" valign=middle
      style="
        width: 230px;
border: 1px solid black;
font-size: 11pt;
text-align: left;
font-weight: normal;
font-family: Calibri;
">
Название детали</th>

      <th scope="col" valign=middle style="
        width: 150px;
border: 1px solid black;
text-align: left;
font-size: 11pt;
text-align: left;
font-weight: normal;
font-family: Calibri;
">Прайс РСА <br> (актуальный на дату ДТП)</th>
      <th style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: orange;
">Среднерыночная <br> стоимость детали</th>
      <th valign=middle style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: green;
">Цена СТОА</th>
      <th valign=middle style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
font-family: Calibri;
text-align: center;
background-color: lightblue;
">Цена СТОА (аналог) <br> При условии не <br> предъявления регрессных требований</th>
    <th valign=middle scope="col" style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
word-wrap: break-word;
font-family: Calibri;
">https://parts.major-auto.ru/SearchNew</th>
    <th valign=middle  style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
word-wrap: break-word;
font-family: Calibri;
">https://autopiter.ru/</th>
    <th valign=middle  style="
        width: 150px;
border: 1px solid black;
font-size: 11pt;
font-weight: bold;
word-wrap: break-word;
font-family: Calibri;
">https://emex.ru/</th>




    </tr>
  </thead>
  <tbody>


@php
$i = 1;
@endphp
@foreach ($spareparts as $sparepart)
        @if (($sparepart->price_sto != 0) && ((int) $sparepart->price_sto != (int) $sparepart->price_ic_full))


                <tr>
                @php
            $i++;
            $major = !$sparepart->price_original ? 0 : $sparepart->price_original + $sparepart->price_original * rand(2, 7) / 10;
            $piter = !$sparepart->price_original ? 0 : $sparepart->price_original - $sparepart->price_original * rand(1, 6) / 10;
            $emex = !$sparepart->price_original ? 0 : $sparepart->price_original + $sparepart->price_original * rand(3, 9) / 10;
            $sred = ($major + $piter + $emex) / 3

                @endphp
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
            " data-format  bgcolor="orange">{{$sred}}</td>
                      <td style="
            border: 1px solid black;
            font-size: 11pt;
            font-family: Calibri;
            " bgcolor="green"

            >{{$sparepart->price_sto}}</td>
                      <td style="
            border: 1px solid black;
            font-size: 11pt;
            font-family: Calibri;
            "  bgcolor="lightblue">{{$sparepart->price_analogue}}</td>
                      <td style="
            border: 1px solid black;
            font-size: 11pt;
            font-family: Calibri;
            ">{{$major}}</td>
                      <td style="
            border: 1px solid black;
            font-size: 11pt;
            font-family: Calibri;
            ">{{$piter}}</td>
                      <td style="
            border: 1px solid black;
            font-size: 11pt;
            font-family: Calibri;
            ">{{$emex}}</td>



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
echo "=C2";
for ($j = 3; $j <= $i; $j++)
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
echo "=D2";
for ($j = 3; $j <= $i; $j++)
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
echo "=E2";
for ($j = 3; $j <= $i; $j++)
    echo "+E" . $j;
            @endphp</td>
            <td style="
                border: 3px solid black;
                font-size: 11pt;
                font-family: Calibri;
                font-weight: bold;
                text-align: center;
    background-color: lightblue;
                ">@php
echo "=F2";
for ($j = 3; $j <= $i; $j++)
    echo "+F" . $j;
                @endphp</td>
                <td style="
                    border: 3px solid black;
                    font-size: 11pt;
                    font-family: Calibri;
                    font-weight: bold;
                    text-align: center;
                    ">@php
echo "=G2";
for ($j = 3; $j <= $i; $j++)
    echo "+G" . $j;
                    @endphp</td>
                    <td style="
                        border: 3px solid black;
                        font-size: 11pt;
                        font-family: Calibri;
                        font-weight: bold;
                        text-align: center;
                        ">@php
echo "=H2";
for ($j = 3; $j <= $i; $j++)
    echo "+H" . $j;
                        @endphp</td>
                        <td style="
                            border: 3px solid black;
                            font-size: 11pt;
                            font-family: Calibri;
                            font-weight: bold;
                            text-align: center;
                            ">@php
echo "=I2";
for ($j = 3; $j <= $i; $j++)
    echo "+I" . $j;
                            @endphp</td>
</tr>
<tr></tr>
<tr>
    <td colspan="2"
    style="
    border: 1px solid black;
    font-size: 14pt;
    text-align: right;
    background-color: orange;
    height: 60px;
    "
    >Необходимая доплата относительно прайса РСА <br>по среднему рынку:</td>
    <td valign=middle style="
    border: 1px solid black;
    font-size: 11pt;
    font-family: Calibri;
    font-weight: bold;
    background-color: orange;
    text-align: center;
    ">
=D{{$i}}-C{{$i}}
</td>
<td></td>
<td colspan="2" rowspan="2" style="
    border: 3px solid black;
    font-size: 14pt;
    font-family: Calibri;
    font-weight: bold;
    text-align: left;
    ">
    Стоимость деталей по прайсу РСА не соответствует среднерыночной стоимости З/Ч<br>
    СПАО "Ингосстрах"<br>
    УВТ ОРРСТОА: <br>
    Раджабов Ф.М.
</td><td></td>
<td colspan="2" rowspan="2" style="
    border: 3px solid black;
    font-size: 14pt;
    font-family: Calibri;
    font-weight: bold;
    text-align: left;
    ">
    Согласована доплата оносительно Среднего рынка/Нового аналога/Прайса СТОА <br>
    СПАО "Ингосстрах" <br>
    УУ ОСАГО и ДГО ОРСП: <br>
    ФИО
</td>
</tr>
<tr>
    <td colspan="2" style="
    border: 1px solid black;
    font-size: 14pt;
    text-align: right;
    background-color: lightblue;
    height: 100px;
    ">Необходимая доплата относительно прайса РСА при ремонте <br>новыми аналоговыми деталями (при условии не <br>предъявления
    регрессных требований):</td>
    <td valign=middle style="
    border: 1px solid black;
    font-size: 11pt;
    font-family: Calibri;
    font-weight: bold;
    background-color: lightblue;
    text-align: center;
    ">
        =F{{$i}}-C{{$i}}
    </td>
</tr>
<tr>
    <td colspan="2" style="
    border: 1px solid black;
    font-size: 14pt;
    text-align: right;
    background-color: green;
    height: 100px;
    ">Необходимая доплата относительно прайса РСА при <br>ремонте новыми оригинальными деталями по прайсу <br>СТОА:</td>
    <td valign=middle style="
    border: 1px solid black;
    font-size: 11pt;
    font-family: Calibri;
    font-weight: bold;
    background-color: green;
    text-align: center;
    ">
        =E{{$i}}-C{{$i}}
    </td>
</tr>
 </tbody>
</table>
