Новое сообщение в чате заявки {{ $app_number }}
<br>

<b><i>Автор:</b></i> {{ $author }}
<br>
<b><i>Сообщение:</b></i>
<br>
@php
    echo '<br>' . $message . '<br>';
@endphp

<br> <b><i>Конец сообщения</b></i><br>

<a href="http://mandarin-portal.ru/chat/{{ $app_number }}" target="_blank">Перейти к чату можно по ссылке</a>
<br>
С уважением,<br>
{{ config('app.name') }}
