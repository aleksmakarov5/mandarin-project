@extends('personal.header_menu')
@section('title')
    Личный кабинет
@endsection
@section('main')
    <h1 class="h3 mb-3 fw-normal">Телеграм</h1>

    @if (isset(Auth::user()->telegram_id))
        <a href="/personal/telegram_delete" class="nav-link">Удалить телеграмм аккаунт</a>
        @if (isset(Auth::user()->avatar))
            <img src="{{ Auth::user()->avatar }}" class="avatar">
        @endif
        {{ Auth::user()->nickname }}

        <form method="POST" action="/personal/telegram_update" name='form' id='form'>
            @csrf
            <div class='lbl'>
                <label for="id_spm">Получать уведомления в Телеграм</label>
            </div>
            <div class="form-float">

                <select class="form-cont" id="telegram_notify" name="telegram_notify"
                    onchange="document.forms['form'].submit();">

                    <option value="1" @if (Auth::user()->telegram_notify == 1) selected @endif>
                        Да
                    </option>
                    <option value="0" @if (Auth::user()->telegram_notify == 0) selected @endif>
                        Нет
                    </option>


                </select>

            </div>
            <button class="nav-link" type="submit">Обновить</button>
        </form>
    @else
        <a href="/auth/telegram/redirect" class="nav-link">Добавить телеграмм аккаунт</a>
    @endif
@endsection
