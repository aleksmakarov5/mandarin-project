<x-mail::message>
    Новое сообщение в чате заявки {{$app_number}}

Автор: {{$author}}

Сообщение:

{{$message}}



<a href="http://127.0.0.1:8000/chat/{{$app_number}}" target="_blank">Перейти к чату можно по ссылке</a>

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
