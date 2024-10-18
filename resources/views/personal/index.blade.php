@extends('personal.header_menu')
@section('title')
    Личный кабинет
@endsection
@section('main')
    <form method="POST" action="/personal/update" name="form" id="form">

        <h1 class="h3 mb-3 fw-normal">Личный кабинет</h1>
        @csrf
        {{-- Выберите тему
        <div class="form_radio_btn dark">
            <input id="radio-5" type="radio" name="theme" value="dark" @if ($user->theme == 'dark') checked @endif
                onchange="document.forms['form'].submit();">
            <label for="radio-5">темная</label>
        </div>
        <div class="form_radio_btn light">
            <input id="radio-6" type="radio" name="theme" value="light" onchange="document.forms['form'].submit();"
                @if ($user->theme == 'light') checked @endif>
            <label for="radio-6">светлая</label>
        </div> --}}
        <br>
        <br>
        <br>
        <div class="form-float">
            <input type="text" name='name' id="name" class="form-cont" id="floatingInput" placeholder="ФИО"
                value="{{ $user->name }}">
            <label for="floatingInput">ФИО</label>
            @error('name')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-float">
            <input type="email" name='email' id="email" class="form-cont" id="floatingInput"
                placeholder="name@example.com" value="{{ $user->email }}">
            <label for="floatingInput">Электронный адрес</label>
            @error('email')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-float">
            <input type="text" name='phone' id="phone" class="form-cont" id="floatingInput"
                value="{{ $user->phone }}" placeholder="+7(000)000-00-00" data-phone-pattern>
            <label for="floatingInput">Номер телефона</label>
            @error('phone')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>


        <button class="nav-link" type="submit">Обновить</button>
    </form>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var eventCalllback = function(e) {
                var el = e.target,
                    clearVal = el.dataset.phoneClear,
                    pattern = el.dataset.phonePattern,
                    matrix_def = "+7(___) ___-__-__",
                    matrix = pattern ? pattern : matrix_def,
                    i = 0,
                    def = matrix.replace(/\D/g, ""),
                    val = e.target.value.replace(/\D/g, "");
                if (clearVal !== 'false' && e.type === 'blur') {
                    if (val.length < matrix.match(/([\_\d])/g).length) {
                        e.target.value = '';
                        return;
                    }
                }
                if (def.length >= val.length) val = def;
                e.target.value = matrix.replace(/./g, function(a) {
                    return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i >= val.length ? "" :
                        a
                });
            }
            var phone_inputs = document.querySelectorAll('[data-phone-pattern]');
            for (let elem of phone_inputs) {
                for (let ev of ['input', 'blur', 'focus']) {
                    elem.addEventListener(ev, eventCalllback);
                }
            }
        });
    </script>
@endsection
