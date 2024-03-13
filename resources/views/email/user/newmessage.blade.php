<x-mail::message>
    Новое сообщение в чате заявки {{ $app_number }}

    Автор: {{ $author }}

    Сообщение:

    {{ $message }}



    <a href="http://mandarin-portal.ru/chat/{{ $app_number }}" target="_blank">Перейти к чату можно по ссылке</a>

    С уважением,<br>
    {{ config('app.name') }}
</x-mail::message>
