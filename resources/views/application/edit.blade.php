@extends('header_menu')
@section('main')
    <a href="/application/{{ $application->id }}" class="nav-link">Отмена</a>
    <form action="/applications/{{ $application->id }}/update" method="POST">
        @csrf

        @include('application.form')

        <input type="submit" value="Обновить" class="nav-link">
    </form>



    {{-- <script>
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
    </script> --}}
@endsection
